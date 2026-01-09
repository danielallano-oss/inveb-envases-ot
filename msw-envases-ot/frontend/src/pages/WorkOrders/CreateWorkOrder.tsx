/**
 * CreateWorkOrder Component
 * Formulario completo para crear una nueva Orden de Trabajo
 * Basado en el diseño de Laravel ficha-form.blade.php con estilos Monitor One
 */

import { useState, useCallback, useEffect, useMemo } from 'react';
import { useMutation, useQueryClient } from '@tanstack/react-query';
import styled from 'styled-components';
import { theme } from '../../theme';
import { CascadeForm } from '../../components/CascadeForm';
import { MuestraModal } from '../../components/MuestraModal';
import SearchableSelect from '../../components/SearchableSelect';
import { workOrdersApi, cascadesApi, cascadeApi, uploadsApi, type WorkOrderCreateData, type DatosOTFromCotizacion, type InstalacionOption, type ContactoOption, type OTFileType } from '../../services/api';
import { useWorkOrderFilterOptions, useFormOptionsComplete } from '../../hooks/useWorkOrders';
import type { CascadeFormData } from '../../types/cascade';

// Constantes de roles (equivalentes a Laravel Constants.php)
const ROLES = {
  Admin: 1,
  Gerente: 2,
  JefeVenta: 3,
  Vendedor: 4,
  JefeDesarrollo: 5,
  Ingeniero: 6,  // Dibujante Técnico (Diseño estructural)
  JefeDiseño: 7,
  Diseñador: 8,  // D.G.
  JefeCatalogador: 9,
  Catalogador: 10,
  JefePrecatalogador: 11,
  Precatalogador: 12,
  JefeMuestras: 13,
  TecnicoMuestras: 14,
  GerenteComercial: 15,
  API: 17,
  SuperAdministrador: 18,
  VendedorExterno: 19,
} as const;

// Helper para obtener el rol del usuario actual
const getCurrentUserRole = (): number => {
  try {
    const userStr = localStorage.getItem('inveb_ot_user');
    if (userStr) {
      const user = JSON.parse(userStr);
      return user.role_id || ROLES.Vendedor;
    }
  } catch {
    // Si hay error, asumir vendedor
  }
  return ROLES.Vendedor;
};

// Styled Components
const Container = styled.div`
  padding: 1.5rem;
  max-width: 100%;
`;

const Header = styled.div`
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.5rem;
  padding-bottom: 1rem;
  border-bottom: 2px solid ${theme.colors.primary};
`;

const Title = styled.h1`
  font-size: 1.5rem;
  font-weight: 600;
  color: ${theme.colors.textPrimary};
  margin: 0;
`;

const BackButton = styled.button`
  padding: 0.5rem 1.25rem;
  border: 1px solid ${theme.colors.border};
  border-radius: 50px;
  background: white;
  color: ${theme.colors.textSecondary};
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s;

  &:hover {
    border-color: ${theme.colors.primary};
    color: ${theme.colors.primary};
  }
`;

const FormSection = styled.div`
  background: white;
  border: 1px solid ${theme.colors.border};
  border-radius: 8px;
  margin-bottom: 1rem;
`;

const SectionHeader = styled.div`
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  color: white;
  padding: 0.75rem 1rem;
  font-weight: 500;
  font-size: 0.875rem;
  border-radius: 8px 8px 0 0;
`;

const SectionBody = styled.div`
  padding: 1rem;
`;

const FormGrid = styled.div<{ $columns?: number }>`
  display: grid;
  grid-template-columns: repeat(${props => props.$columns || 3}, 1fr);
  gap: 1rem;

  @media (max-width: 1024px) {
    grid-template-columns: repeat(2, 1fr);
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
`;

// Grid específico para Sección 8 con 4 columnas de igual ancho
const FormGridSection8 = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr 1fr 1fr;
  gap: 0.75rem;
  width: 100%;
  max-width: 100%;
  overflow: hidden;

  & > div {
    min-width: 0;
    overflow: hidden;
  }

  @media (max-width: 1200px) {
    grid-template-columns: repeat(2, 1fr);
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
`;

// Contenedor para secciones 9, 10 y 11 en fila horizontal
const SectionsRow = styled.div`
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 1rem;
  width: 100%;

  @media (max-width: 1200px) {
    grid-template-columns: 1fr 1fr;
  }

  @media (max-width: 768px) {
    grid-template-columns: 1fr;
  }
`;

// FormSection compacta para secciones pequeñas
const FormSectionCompact = styled.div`
  background: white;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  overflow: hidden;
`;

const FormGroup = styled.div`
  display: flex;
  flex-direction: column;
`;

const Label = styled.label`
  font-size: 0.75rem;
  font-weight: 500;
  color: ${theme.colors.textSecondary};
  margin-bottom: 0.25rem;
  text-transform: uppercase;
`;

const Input = styled.input<{ $hasError?: boolean }>`
  padding: 0.5rem;
  border: 1px solid ${props => props.$hasError ? '#dc3545' : theme.colors.border};
  border-radius: 4px;
  font-size: 0.875rem;
  transition: border-color 0.2s;
  background: ${props => props.$hasError ? '#fff5f5' : 'white'};

  &:focus {
    outline: none;
    border-color: ${props => props.$hasError ? '#dc3545' : theme.colors.primary};
    box-shadow: 0 0 0 2px ${props => props.$hasError ? '#dc354520' : theme.colors.primary + '20'};
  }

  &:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
  }
`;

const Select = styled.select<{ $hasError?: boolean }>`
  padding: 0.5rem;
  border: 1px solid ${props => props.$hasError ? '#dc3545' : theme.colors.border};
  border-radius: 4px;
  font-size: 0.875rem;
  background: ${props => props.$hasError ? '#fff5f5' : 'white'};
  transition: border-color 0.2s;

  &:focus {
    outline: none;
    border-color: ${props => props.$hasError ? '#dc3545' : theme.colors.primary};
    box-shadow: 0 0 0 2px ${props => props.$hasError ? '#dc354520' : theme.colors.primary + '20'};
  }

  &:disabled {
    background: #f5f5f5;
    cursor: not-allowed;
  }
`;

const TextArea = styled.textarea<{ $hasError?: boolean }>`
  padding: 0.5rem;
  border: 1px solid ${props => props.$hasError ? '#dc3545' : theme.colors.border};
  border-radius: 4px;
  font-size: 0.875rem;
  resize: vertical;
  min-height: 80px;
  transition: border-color 0.2s;
  background: ${props => props.$hasError ? '#fff5f5' : 'white'};

  &:focus {
    outline: none;
    border-color: ${props => props.$hasError ? '#dc3545' : theme.colors.primary};
    box-shadow: 0 0 0 2px ${props => props.$hasError ? '#dc354520' : theme.colors.primary + '20'};
  }
`;

// Mensaje de error por campo
const FieldError = styled.span`
  font-size: 0.7rem;
  color: #dc3545;
  margin-top: 0.25rem;
  display: block;
`;

const CheckboxGroup = styled.div`
  display: flex;
  flex-wrap: wrap;
  gap: 1rem;
`;

const CheckboxLabel = styled.label`
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
  color: ${theme.colors.textPrimary};
  cursor: pointer;

  input {
    width: 18px;
    height: 18px;
    cursor: pointer;
  }
`;

const SubmitButton = styled.button`
  padding: 0.75rem 2rem;
  background: ${theme.colors.primary};
  color: white;
  border: none;
  border-radius: 50px;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;

  &:hover:not(:disabled) {
    background: #002d66;
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
`;

const ButtonGroup = styled.div`
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
  padding-top: 1rem;
  border-top: 1px solid ${theme.colors.border};
`;

const Alert = styled.div<{ $type: 'success' | 'error' }>`
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1rem;
  font-size: 0.875rem;

  ${props => props.$type === 'success' && `
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
  `}

  ${props => props.$type === 'error' && `
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
  `}
`;

// Types
interface CreateWorkOrderProps {
  onNavigate: (page: string, otId?: number) => void;
  initialData?: DatosOTFromCotizacion;
}

interface FormState {
  // Datos Comerciales
  client_id: number | null;
  descripcion: string;
  tipo_solicitud: number | null;
  canal_id: number | null;
  org_venta_id: number | null;
  instalacion_cliente_id: number | null;
  contactos_cliente_id: number | null;
  nombre_contacto: string;
  email_contacto: string;
  telefono_contacto: string;
  volumen_venta_anual: number | null;
  usd_monto: string;
  codigo_producto: string;
  oc: number | null;  // Campo OC (0=No, 1=Si)
  // Jerarquías
  hierarchy_id: number | null;
  subhierarchy_id: number | null;
  subsubhierarchy_id: number | null;
  // Antecedentes Desarrollo - Documentos
  ant_des_correo_cliente: boolean;
  ant_des_plano_actual: boolean;
  ant_des_boceto_actual: boolean;
  ant_des_spec: boolean;
  ant_des_otro: boolean;
  // Antecedentes Desarrollo - Muestra Competencia
  ant_des_cj_referencia_de: boolean;
  ant_des_cj_referencia_dg: boolean;
  ant_des_envase_primario: boolean;
  // Antecedentes Desarrollo - Conservar Muestra
  ant_des_conservar_muestra: boolean | null;
  // Armado Automático
  armado_automatico: boolean | null;
  // Solicita (checkboxes)
  analisis: boolean;
  prueba_industrial: boolean;
  muestra: boolean;
  numero_muestras: number | null;
  // Referencia Material (Sección 5)
  reference_type: number | null;
  reference_id: number | null;
  bloqueo_referencia: number | null;
  indicador_facturacion: number | null;
  // Archivo Visto Bueno
  ant_des_vb_muestra: boolean;
  ant_des_vb_boceto: boolean;
  // Issue 8, 11, 18, 19: Campos de archivo para upload
  file_correo_cliente: File | null;
  file_plano_actual: File | null;
  file_boceto_actual: File | null;
  file_otro: File | null;
  file_oc: File | null;  // Issue 8: Orden de Compra
  file_vb_muestra: File | null;  // Issue 18
  file_vb_boceto: File | null;  // Issue 19
  // Cascade fields (Sección 6)
  cascadeData: CascadeFormData;
  // Seccion 7: Caracteristicas
  cad_id: number | null;
  cad_text: string;
  matriz_id: number | null;
  tipo_matriz_text: string;
  items_set: number | null;
  veces_item: number | null;
  style_id: number | null;
  caracteristicas_adicionales: string;
  largura_hm: number | null;
  anchura_hm: number | null;
  area_producto_m2: number | null;
  recorte_adicional_m2: number | null;
  longitud_pegado_mm: number | null;
  golpes_largo: number | null;
  golpes_ancho: number | null;
  separacion_golpes_largo: number | null;
  separacion_golpes_ancho: number | null;
  cuchillas_ml: number | null;
  rayado_c1_r1: number | null;
  rayado_r1_r2: number | null;
  rayado_r2_c2: number | null;
  pallet_qa_id: number | null;
  pais_id: number | null;
  restriccion_pallet: number | null;
  tamano_pallet_type_id: number | null;
  altura_pallet: number | null;
  permite_sobresalir_carga: number | null;
  bulto_zunchado_pallet: number | null;
  formato_etiqueta_pallet: number | null;
  etiquetas_por_pallet: number | null;
  termocontraible: number | null;
  // Especificaciones tecnicas (columna derecha seccion 7)
  bct_min_lb: number | null;
  bct_min_kg: number | null;
  bct_humedo_lb: number | null;
  ect_min_lb_pulg: number | null;
  gramaje_g_m2: number | null;
  mullen_lb_pulg2: number | null;
  fct_lb_pulg2: number | null;
  dst_bpi: number | null;
  espesor_placa_mm: number | null;
  espesor_caja_mm: number | null;
  cobb_interior_g_m2: number | null;
  cobb_exterior_g_m2: number | null;
  flexion_aleta_n: number | null;
  peso_cliente_g: number | null;
  // Especificaciones tecnicas adicionales (columna 3)
  incision_rayado_longitudinal: string;
  incision_rayado_transversal: string;
  porosidad: string;
  brillo: string;
  rigidez_4_ptos_long: string;
  rigidez_4_ptos_transv: string;
  angulo_deslizamiento_exterior: string;
  angulo_deslizamiento_interior: string;
  resistencia_frote: string;
  contenido_reciclado: string;
  // Medidas
  interno_largo: number | null;
  interno_ancho: number | null;
  interno_alto: number | null;
  externo_largo: number | null;
  externo_ancho: number | null;
  externo_alto: number | null;
  // Terminaciones
  process_id: number | null;
  armado_id: number | null;
  sentido_armado: number | null;
  // Colores
  numero_colores: number | null;
  color_1_id: number | null;
  color_2_id: number | null;
  color_3_id: number | null;
  color_4_id: number | null;
  color_5_id: number | null;
  color_6_id: number | null;
  // Seccion 8: Color-Cera-Barniz
  trazabilidad: number | null;
  design_type_id: number | null;
  complejidad: string;
  percentage_coverage_internal: number | null;
  percentage_coverage_external: number | null;
  impresion_1: number | null;
  impresion_2: number | null;
  impresion_3: number | null;
  impresion_4: number | null;
  impresion_5: number | null;
  impresion_6: number | null;
  cm2_clisse_color_1: number | null;
  cm2_clisse_color_2: number | null;
  cm2_clisse_color_3: number | null;
  cm2_clisse_color_4: number | null;
  cm2_clisse_color_5: number | null;
  cm2_clisse_color_6: number | null;
  cm2_clisse_color_7: number | null;
  barniz_uv: number | null;
  porcentaje_barniz_uv: number | null;
  indicador_facturacion_diseno_grafico: string;
  prueba_color: number | null;
  impresion_borde: string;
  impresion_sobre_rayado: string;
  total_cm2_clisse: number | null;
  pegado_terminacion: number | null;
  maquila: number | null;
  maquila_servicio_id: number | null;
  // Sección 13 - Material Asignado
  material_asignado: string;
  descripcion_material: string;
  // Sección 13 - Datos para Desarrollo
  product_type_developing_id: number | null;
  food_type_id: number | null;
  expected_use_id: number | null;
  recycled_use_id: number | null;
  class_substance_packed_id: number | null;
  transportation_way_id: number | null;
  peso_contenido_caja: number | null;
  autosoportante: boolean | null;
  envase_id: number | null;
  cantidad_cajas_apiladas: number | null;
  pallet_sobre_pallet: boolean | null;
  cantidad: number | null;
  target_market_id: number | null;
  // Observaciones
  observacion: string;
  // Planta
  planta_id: number | null;
  // Secuencia Operacional (Seccion 12)
  // Estructura: 6 filas x 6 columnas (Original, Alt1, Alt2, Alt3, Alt4, Alt5)
  so_planta_original: number | null;
  so_planta_original_select_values: string;  // JSON array para guardar valores
  so_planta_alt1: number | null;
  so_planta_alt1_select_values: string;
  so_planta_alt2: number | null;
  so_planta_alt2_select_values: string;
  // Matriz de secuencia operacional: secuencia_operacional[fila][columna]
  secuencia_operacional_matrix: (string | null)[][];
  // Issue 42: Distancia Cinta (visible cuando cinta = 1)
  distancia_cinta_1: number | null;
  distancia_cinta_2: number | null;
  distancia_cinta_3: number | null;
  distancia_cinta_4: number | null;
  distancia_cinta_5: number | null;
  distancia_cinta_6: number | null;
}

const INITIAL_STATE: FormState = {
  client_id: null,
  descripcion: '',
  tipo_solicitud: null,
  canal_id: null,
  org_venta_id: null,
  instalacion_cliente_id: null,
  contactos_cliente_id: null,
  nombre_contacto: '',
  email_contacto: '',
  telefono_contacto: '',
  volumen_venta_anual: null,
  usd_monto: '',
  codigo_producto: '',
  oc: null,
  // Jerarquías
  hierarchy_id: null,
  subhierarchy_id: null,
  subsubhierarchy_id: null,
  // Antecedentes Desarrollo - Documentos
  ant_des_correo_cliente: false,
  ant_des_plano_actual: false,
  ant_des_boceto_actual: false,
  ant_des_spec: false,
  ant_des_otro: false,
  // Antecedentes Desarrollo - Muestra Competencia
  ant_des_cj_referencia_de: false,
  ant_des_cj_referencia_dg: false,
  ant_des_envase_primario: false,
  // Antecedentes Desarrollo - Conservar Muestra
  ant_des_conservar_muestra: null,
  // Armado Automático
  armado_automatico: null,
  // Solicita
  analisis: false,
  prueba_industrial: false,
  muestra: false,
  numero_muestras: null,
  // Referencia Material (Sección 5)
  reference_type: null,
  reference_id: null,
  bloqueo_referencia: null,
  indicador_facturacion: null,
  // Archivo Visto Bueno
  ant_des_vb_muestra: false,
  ant_des_vb_boceto: false,
  // Issue 8, 11, 18, 19: Archivos para upload
  file_correo_cliente: null,
  file_plano_actual: null,
  file_boceto_actual: null,
  file_otro: null,
  file_oc: null,
  file_vb_muestra: null,
  file_vb_boceto: null,
  cascadeData: {
    productTypeId: null,
    impresion: null,
    fsc: null,
    cinta: null,
    coverageInternalId: null,
    coverageExternalId: null,
    plantaId: null,
    cartonColor: null,
    cartonId: null,
  },
  // Seccion 7: Caracteristicas
  cad_id: null,
  cad_text: '',
  matriz_id: null,
  tipo_matriz_text: '',
  items_set: null,
  veces_item: null,
  style_id: null,
  caracteristicas_adicionales: '',
  largura_hm: null,
  anchura_hm: null,
  area_producto_m2: null,
  recorte_adicional_m2: null,
  longitud_pegado_mm: null,
  golpes_largo: null,
  golpes_ancho: null,
  separacion_golpes_largo: null,
  separacion_golpes_ancho: null,
  cuchillas_ml: null,
  rayado_c1_r1: null,
  rayado_r1_r2: null,
  rayado_r2_c2: null,
  pallet_qa_id: null,
  pais_id: null,
  restriccion_pallet: null,
  tamano_pallet_type_id: null,
  altura_pallet: null,
  permite_sobresalir_carga: null,
  bulto_zunchado_pallet: null,
  formato_etiqueta_pallet: null,
  etiquetas_por_pallet: null,
  termocontraible: null,
  // Especificaciones tecnicas
  bct_min_lb: null,
  bct_min_kg: null,
  bct_humedo_lb: null,
  ect_min_lb_pulg: null,
  gramaje_g_m2: null,
  mullen_lb_pulg2: null,
  fct_lb_pulg2: null,
  dst_bpi: null,
  espesor_placa_mm: null,
  espesor_caja_mm: null,
  cobb_interior_g_m2: null,
  cobb_exterior_g_m2: null,
  flexion_aleta_n: null,
  peso_cliente_g: null,
  // Especificaciones tecnicas adicionales
  incision_rayado_longitudinal: '',
  incision_rayado_transversal: '',
  porosidad: '',
  brillo: '',
  rigidez_4_ptos_long: '',
  rigidez_4_ptos_transv: '',
  angulo_deslizamiento_exterior: '',
  angulo_deslizamiento_interior: '',
  resistencia_frote: '',
  contenido_reciclado: '',
  interno_largo: null,
  interno_ancho: null,
  interno_alto: null,
  externo_largo: null,
  externo_ancho: null,
  externo_alto: null,
  process_id: null,
  armado_id: null,
  sentido_armado: null,
  numero_colores: null,
  color_1_id: null,
  color_2_id: null,
  color_3_id: null,
  color_4_id: null,
  color_5_id: null,
  color_6_id: null,
  // Seccion 8: Color-Cera-Barniz
  trazabilidad: null,
  design_type_id: null,
  complejidad: '',
  percentage_coverage_internal: null,
  percentage_coverage_external: null,
  impresion_1: null,
  impresion_2: null,
  impresion_3: null,
  impresion_4: null,
  impresion_5: null,
  impresion_6: null,
  cm2_clisse_color_1: null,
  cm2_clisse_color_2: null,
  cm2_clisse_color_3: null,
  cm2_clisse_color_4: null,
  cm2_clisse_color_5: null,
  cm2_clisse_color_6: null,
  cm2_clisse_color_7: null,
  barniz_uv: null,
  porcentaje_barniz_uv: null,
  indicador_facturacion_diseno_grafico: '',
  prueba_color: null,
  impresion_borde: '',
  impresion_sobre_rayado: '',
  total_cm2_clisse: null,
  pegado_terminacion: null,
  maquila: null,
  maquila_servicio_id: null,
  // Sección 13 - Material Asignado
  material_asignado: '',
  descripcion_material: '',
  // Sección 13 - Datos para Desarrollo
  product_type_developing_id: null,
  food_type_id: null,
  expected_use_id: null,
  recycled_use_id: null,
  class_substance_packed_id: null,
  transportation_way_id: null,
  peso_contenido_caja: null,
  autosoportante: null,
  envase_id: null,
  cantidad_cajas_apiladas: null,
  pallet_sobre_pallet: null,
  cantidad: null,
  target_market_id: null,
  observacion: '',
  planta_id: null,
  // Secuencia Operacional
  so_planta_original: null,
  so_planta_original_select_values: '',
  so_planta_alt1: null,
  so_planta_alt1_select_values: '',
  so_planta_alt2: null,
  so_planta_alt2_select_values: '',
  // Matriz 6x6: filas x columnas (Original, Alt1-5)
  secuencia_operacional_matrix: Array(6).fill(null).map(() => Array(6).fill(null)),
  // Issue 42: Distancia Cinta
  distancia_cinta_1: null,
  distancia_cinta_2: null,
  distancia_cinta_3: null,
  distancia_cinta_4: null,
  distancia_cinta_5: null,
  distancia_cinta_6: null,
};

// Tipos de solicitud base (igual que Laravel WorkOrderController@select)
// El orden es importante: 1, 3, 7, 5, 6
const TIPO_SOLICITUD_OPTIONS_BASE = [
  { id: 1, nombre: 'Desarrollo Completo' },
  { id: 3, nombre: 'Muestra con CAD' },
  { id: 7, nombre: 'OT Proyectos Innovación' },
  { id: 5, nombre: 'Arte con Material' },
  { id: 6, nombre: 'Otras Solicitudes Desarrollo' },
];

// Obtener tipos de solicitud filtrados por rol (igual que Laravel)
const getTipoSolicitudOptions = (roleId: number, areaId?: number) => {
  // Área de desarrollo (area_id = 2): sin "Arte con Material"
  if (areaId === 2) {
    return TIPO_SOLICITUD_OPTIONS_BASE.filter(t => t.id !== 5);
  }
  // Vendedor Externo: solo "Desarrollo Completo" y "Arte con Material"
  if (roleId === ROLES.VendedorExterno) {
    return TIPO_SOLICITUD_OPTIONS_BASE.filter(t => t.id === 1 || t.id === 5);
  }
  // Default: todos los tipos
  return TIPO_SOLICITUD_OPTIONS_BASE;
};

// Styled component para el boton de reiniciar
const ResetButton = styled.button`
  padding: 0.5rem 1rem;
  background: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 0.875rem;
  cursor: pointer;
  transition: background 0.2s;

  &:hover {
    background: #218838;
  }
`;

// Grid especifico para la seccion inicial
const InitialSectionGrid = styled.div`
  display: flex;
  gap: 1rem;
  align-items: flex-end;
`;

// Tipo para errores de campo
type FieldErrors = Record<string, string>;

export default function CreateWorkOrder({ onNavigate, initialData }: CreateWorkOrderProps) {
  const [formState, setFormState] = useState<FormState>(INITIAL_STATE);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});

  // Estados para instalaciones y contactos del cliente (carga dinámica)
  const [instalaciones, setInstalaciones] = useState<InstalacionOption[]>([]);
  const [contactos, setContactos] = useState<ContactoOption[]>([]);
  const [loadingInstalaciones, setLoadingInstalaciones] = useState(false);
  const [loadingContactos, setLoadingContactos] = useState(false);

  // Estado para el modal de Crear Muestra
  const [showMuestraModal, setShowMuestraModal] = useState(false);

  const queryClient = useQueryClient();
  const { data: filterOptions, isLoading: optionsLoading } = useWorkOrderFilterOptions();
  const { data: formOptions, isLoading: formOptionsLoading } = useFormOptionsComplete();

  // Filtrar subjerarquías basado en jerarquía seleccionada
  const filteredSubhierarchies = useMemo(() => {
    if (!formOptions?.subhierarchies || !formState.hierarchy_id) return [];
    return formOptions.subhierarchies.filter(
      sh => sh.hierarchy_id === formState.hierarchy_id
    );
  }, [formOptions?.subhierarchies, formState.hierarchy_id]);

  // Filtrar subsubjerarquías basado en subjerarquía seleccionada
  const filteredSubsubhierarchies = useMemo(() => {
    if (!formOptions?.subsubhierarchies || !formState.subhierarchy_id) return [];
    return formOptions.subsubhierarchies.filter(
      ssh => ssh.subhierarchy_id === formState.subhierarchy_id
    );
  }, [formOptions?.subsubhierarchies, formState.subhierarchy_id]);

  // Tipos de solicitud filtrados por rol del usuario (igual que Laravel)
  const tipoSolicitudOptions = useMemo(() => {
    const role = getCurrentUserRole();
    // TODO: Obtener area_id del usuario si es necesario
    return getTipoSolicitudOptions(role);
  }, []);

  // Reset subjerarquías cuando cambia jerarquía
  useEffect(() => {
    if (formState.hierarchy_id) {
      setFormState(prev => ({
        ...prev,
        subhierarchy_id: null,
        subsubhierarchy_id: null,
      }));
    }
  }, [formState.hierarchy_id]);

  // Abrir modal de Crear Muestra automáticamente cuando se selecciona tipo_solicitud = 3 (Muestra con CAD)
  // Igual que en Laravel ot-duplication.js línea 4261-4263
  useEffect(() => {
    if (formState.tipo_solicitud === 3) {
      // Abrir el modal de crear muestra
      setShowMuestraModal(true);
      // Marcar el checkbox de muestra automáticamente
      setFormState(prev => ({
        ...prev,
        muestra: true,
      }));
    }
  }, [formState.tipo_solicitud]);

  // Reset subsubjerarquía cuando cambia subjerarquía
  useEffect(() => {
    if (formState.subhierarchy_id) {
      setFormState(prev => ({
        ...prev,
        subsubhierarchy_id: null,
      }));
    }
  }, [formState.subhierarchy_id]);

  // Sincronizar Jerarquía 1 con Canal (regla de negocio Laravel)
  // La sincronización es por nombre: el canal "INDUSTRIAL" debe seleccionar la jerarquía "INDUSTRIAL"
  useEffect(() => {
    if (formState.canal_id && filterOptions?.canales && formOptions?.hierarchies) {
      // Encontrar el canal seleccionado
      const selectedCanal = filterOptions.canales.find(c => c.id === formState.canal_id);
      if (selectedCanal) {
        // Buscar la jerarquía con el mismo nombre
        const matchingHierarchy = formOptions.hierarchies.find(
          h => h.nombre?.toUpperCase() === selectedCanal.nombre?.toUpperCase()
        );
        if (matchingHierarchy) {
          setFormState(prev => ({
            ...prev,
            hierarchy_id: Number(matchingHierarchy.id),
            subhierarchy_id: null,
            subsubhierarchy_id: null,
          }));
        }
      }
    }
  }, [formState.canal_id, filterOptions?.canales, formOptions?.hierarchies]);

  // Cargar instalaciones cuando cambia el cliente
  useEffect(() => {
    console.log('[DEBUG] useEffect client_id ejecutado, client_id=', formState.client_id);
    if (formState.client_id) {
      setLoadingInstalaciones(true);
      setInstalaciones([]);
      setContactos([]);
      // Reset campos dependientes
      setFormState(prev => ({
        ...prev,
        instalacion_cliente_id: null,
        contactos_cliente_id: null,
        nombre_contacto: '',
        email_contacto: '',
        telefono_contacto: '',
      }));

      console.log('[DEBUG] Llamando cascadesApi.getInstalacionesCliente con client_id=', formState.client_id);
      cascadesApi.getInstalacionesCliente(formState.client_id)
        .then(data => {
          console.log('[DEBUG] Instalaciones recibidas:', data);
          setInstalaciones(data);
        })
        .catch(err => console.error('[DEBUG] Error cargando instalaciones:', err))
        .finally(() => setLoadingInstalaciones(false));
    } else {
      setInstalaciones([]);
      setContactos([]);
    }
  }, [formState.client_id]);

  // Cargar contactos cuando cambia el cliente o instalación
  useEffect(() => {
    if (formState.client_id) {
      setLoadingContactos(true);
      setContactos([]);

      cascadesApi.getContactosCliente(formState.client_id, formState.instalacion_cliente_id || undefined)
        .then(data => setContactos(data))
        .catch(err => console.error('Error cargando contactos:', err))
        .finally(() => setLoadingContactos(false));
    }
  }, [formState.client_id, formState.instalacion_cliente_id]);

  // Issue 39: Cargar datos de la instalación (incluyendo bulto_zunchado) cuando cambia
  useEffect(() => {
    if (formState.instalacion_cliente_id) {
      cascadesApi.getInformacionInstalacion(formState.instalacion_cliente_id)
        .then(info => {
          setFormState(prev => ({
            ...prev,
            // Issue 39: Auto-cargar bulto_zunchado desde la instalación
            bulto_zunchado_pallet: info.bulto_zunchado ?? null,
            // También cargar otros campos de la instalación si existen
            formato_etiqueta_pallet: info.formato_etiqueta ? Number(info.formato_etiqueta) : prev.formato_etiqueta_pallet,
            etiquetas_por_pallet: info.etiquetas_pallet ?? prev.etiquetas_por_pallet,
          }));
        })
        .catch(err => console.error('Error cargando información de instalación:', err));
    } else {
      // Limpiar valores cuando se deselecciona la instalación
      setFormState(prev => ({
        ...prev,
        bulto_zunchado_pallet: null,
        formato_etiqueta_pallet: null,
        etiquetas_por_pallet: null,
      }));
    }
  }, [formState.instalacion_cliente_id]);

  // Rellenar datos de contacto cuando se selecciona un contacto del dropdown
  useEffect(() => {
    if (formState.contactos_cliente_id) {
      const contactoSeleccionado = contactos.find(c => c.id === formState.contactos_cliente_id);
      if (contactoSeleccionado) {
        setFormState(prev => ({
          ...prev,
          nombre_contacto: contactoSeleccionado.nombre || '',
          email_contacto: contactoSeleccionado.email || '',
          telefono_contacto: contactoSeleccionado.telefono || '',
        }));
      }
    }
  }, [formState.contactos_cliente_id, contactos]);

  // Pre-popular formulario con datos de cotización si vienen
  useEffect(() => {
    if (initialData) {
      setFormState(prev => ({
        ...prev,
        client_id: initialData.client_id || null,
        descripcion: initialData.descripcion || '',
        tipo_solicitud: initialData.tipo_solicitud || null,
        nombre_contacto: initialData.nombre_contacto || '',
        email_contacto: initialData.email_contacto || '',
        telefono_contacto: initialData.telefono_contacto || '',
        codigo_producto: initialData.material_codigo || '',
        // Medidas internas (mapeando nombres de campos)
        interno_largo: initialData.largo_interno || null,
        interno_ancho: initialData.ancho_interno || null,
        interno_alto: initialData.alto_interno || null,
        // Medidas externas
        externo_largo: initialData.largo_externo || null,
        externo_ancho: initialData.ancho_externo || null,
        externo_alto: initialData.alto_externo || null,
        // Desarrollo
        cantidad: initialData.cantidad || null,
      }));
    }
  }, [initialData]);

  // Issue 50: Sincronizar Planta de Sección 12 con Planta de Sección 6
  useEffect(() => {
    if (formState.cascadeData?.plantaId) {
      setFormState(prev => ({
        ...prev,
        so_planta_original: formState.cascadeData.plantaId
      }));
    }
  }, [formState.cascadeData?.plantaId]);

  // Issue 8, 11, 18, 19: Función para subir archivos pendientes
  const uploadPendingFiles = async (otId: number) => {
    const fileUploads: { file: File; type: OTFileType }[] = [];

    if (formState.file_correo_cliente) {
      fileUploads.push({ file: formState.file_correo_cliente, type: 'correo_cliente' });
    }
    if (formState.file_plano_actual) {
      fileUploads.push({ file: formState.file_plano_actual, type: 'plano' });
    }
    if (formState.file_boceto_actual) {
      fileUploads.push({ file: formState.file_boceto_actual, type: 'boceto' });
    }
    if (formState.file_otro) {
      fileUploads.push({ file: formState.file_otro, type: 'otro' });
    }
    if (formState.file_oc) {
      fileUploads.push({ file: formState.file_oc, type: 'oc' });
    }
    if (formState.file_vb_muestra) {
      fileUploads.push({ file: formState.file_vb_muestra, type: 'vb_muestra' });
    }
    if (formState.file_vb_boceto) {
      fileUploads.push({ file: formState.file_vb_boceto, type: 'vb_boceto' });
    }

    // Subir archivos en paralelo
    const uploadPromises = fileUploads.map(({ file, type }) =>
      uploadsApi.uploadOTFile(otId, file, type).catch(err => {
        console.error(`Error subiendo archivo ${type}:`, err);
        return null;
      })
    );

    await Promise.all(uploadPromises);
  };

  // Mutation para crear OT
  const createMutation = useMutation({
    mutationFn: (data: WorkOrderCreateData) => workOrdersApi.create(data),
    onSuccess: async (response) => {
      // Issue 8, 11, 18, 19: Subir archivos pendientes
      const pendingFiles = [
        formState.file_correo_cliente,
        formState.file_plano_actual,
        formState.file_boceto_actual,
        formState.file_otro,
        formState.file_oc,
        formState.file_vb_muestra,
        formState.file_vb_boceto,
      ].filter(Boolean);

      if (pendingFiles.length > 0) {
        setSuccessMessage(`Orden de trabajo #${response.id} creada. Subiendo archivos...`);
        try {
          await uploadPendingFiles(response.id);
          setSuccessMessage(`Orden de trabajo #${response.id} creada exitosamente con ${pendingFiles.length} archivo(s)`);
        } catch (err) {
          console.error('Error subiendo archivos:', err);
          setSuccessMessage(`Orden de trabajo #${response.id} creada, pero algunos archivos no se pudieron subir`);
        }
      } else {
        setSuccessMessage(`Orden de trabajo #${response.id} creada exitosamente`);
      }

      setErrorMessage(null);
      queryClient.invalidateQueries({ queryKey: ['workOrders'] });
      // Redirect after 2 seconds
      setTimeout(() => {
        onNavigate('dashboard');
      }, 2000);
    },
    onError: (error: Error) => {
      setErrorMessage(error.message || 'Error al crear la orden de trabajo');
      setSuccessMessage(null);
    },
  });

  // Handlers
  const handleCascadeChange = useCallback((data: CascadeFormData) => {
    setFormState(prev => ({ ...prev, cascadeData: data }));
  }, []);

  // Issue 26, 45-46: Handler para cambio de CAD - carga datos automáticamente
  const handleCADChange = useCallback(async (cadId: number | null) => {
    // Actualizar el cad_id en el estado
    setFormState(prev => ({ ...prev, cad_id: cadId }));

    // Si se seleccionó un CAD, cargar sus datos
    if (cadId) {
      try {
        const cadDetails = await cascadeApi.getCADDetails(cadId);

        // Actualizar medidas interiores y exteriores desde el CAD
        setFormState(prev => ({
          ...prev,
          cad_text: cadDetails.cad,
          // Issue 45: Medidas interiores desde CAD
          interno_largo: cadDetails.interno_largo || null,
          interno_ancho: cadDetails.interno_ancho || null,
          interno_alto: cadDetails.interno_alto || null,
          // Issue 46: Medidas exteriores desde CAD
          externo_largo: cadDetails.externo_largo || null,
          externo_ancho: cadDetails.externo_ancho || null,
          externo_alto: cadDetails.externo_alto || null,
        }));
      } catch (error) {
        console.error('Error al cargar datos del CAD:', error);
      }
    } else {
      // Limpiar datos si se deselecciona el CAD
      setFormState(prev => ({
        ...prev,
        cad_text: '',
        interno_largo: null,
        interno_ancho: null,
        interno_alto: null,
        externo_largo: null,
        externo_ancho: null,
        externo_alto: null,
      }));
    }
  }, []);

  // Función de validación de campos
  const validateForm = useCallback((): FieldErrors => {
    const errors: FieldErrors = {};
    const role = getCurrentUserRole();
    const tipoSolicitud = formState.tipo_solicitud;

    // Helpers de validacion
    const esDesarrolloCompletoOCotizanSinCad = tipoSolicitud === 1 || tipoSolicitud === 4 || tipoSolicitud === 7;
    const noEsMuestra = tipoSolicitud !== 3;
    const esMuestra = tipoSolicitud === 3;
    const esArteConMaterial = tipoSolicitud === 5;
    const esVendedor = role === ROLES.Vendedor;
    const esVendedorExterno = role === ROLES.VendedorExterno;
    const esIngeniero = role === ROLES.Ingeniero; // Dibujante Técnico (Diseño estructural) - role 6
    const esJefeDesarrollo = role === ROLES.JefeDesarrollo; // role 5
    const esDiseñador = role === ROLES.Diseñador; // D.G. - role 8
    const esJefeVenta = role === ROLES.JefeVenta;

    // ========================================
    // CAMPOS SIEMPRE REQUERIDOS
    // ========================================
    if (!formState.client_id) {
      errors.client_id = 'Campo obligatorio';
    }
    if (!formState.descripcion.trim()) {
      errors.descripcion = 'Campo obligatorio';
    } else if (formState.descripcion.length > 40) {
      errors.descripcion = 'Máximo 40 caracteres';
    }
    if (!tipoSolicitud) {
      errors.tipo_solicitud = 'Campo obligatorio';
    }
    if (!formState.canal_id) {
      errors.canal_id = 'Campo obligatorio';
    }
    // hierarchy_id es requerido si existe
    if (formState.hierarchy_id && !formState.subhierarchy_id) {
      errors.subhierarchy_id = 'Campo obligatorio';
    }
    if (formState.subhierarchy_id && !formState.subsubhierarchy_id) {
      errors.subsubhierarchy_id = 'Campo obligatorio';
    }
    // Contacto - siempre requerido
    if (!formState.nombre_contacto?.trim()) {
      errors.nombre_contacto = 'Campo obligatorio';
    }
    if (!formState.email_contacto?.trim()) {
      errors.email_contacto = 'Campo obligatorio';
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formState.email_contacto)) {
      errors.email_contacto = 'Email inválido';
    }
    if (!formState.telefono_contacto?.trim()) {
      errors.telefono_contacto = 'Campo obligatorio';
    }
    // Observacion - siempre requerida
    if (!formState.observacion?.trim()) {
      errors.observacion = 'Campo obligatorio';
    } else if (formState.observacion.trim().length < 10) {
      errors.observacion = 'Mínimo 10 caracteres';
    } else if (formState.observacion.length > 1000) {
      errors.observacion = 'Máximo 1000 caracteres';
    }
    // Impresion - siempre requerida
    if (!formState.cascadeData?.impresion) {
      errors.impresion = 'Campo obligatorio';
    }
    // FSC - siempre requerido
    if (!formState.cascadeData?.fsc) {
      errors.fsc = 'Campo obligatorio';
    }
    // Sentido armado - siempre requerido
    if (!formState.sentido_armado) {
      errors.sentido_armado = 'Campo obligatorio';
    }

    // ========================================
    // CAMPOS CONDICIONALES POR TIPO SOLICITUD
    // ========================================

    // Si no es muestra, requiere volumen y USD
    if (noEsMuestra && tipoSolicitud) {
      if (!formState.volumen_venta_anual) {
        errors.volumen_venta_anual = 'Campo obligatorio';
      }
      if (!formState.usd_monto?.trim()) {
        errors.usd_monto = 'Campo obligatorio';
      }
    }

    // Desarrollo Completo o Cotizan Sin CAD (tipos 1, 4, 7)
    if (esDesarrolloCompletoOCotizanSinCad) {
      if (!formState.cascadeData?.productTypeId) {
        errors.product_type_id = 'Campo obligatorio';
      }
      // Datos para desarrollo
      if (!formState.peso_contenido_caja) {
        errors.peso_contenido_caja = 'Campo obligatorio';
      }
      // autosoportante es boolean - siempre requerido para estos tipos
      // Validamos que el usuario haya seleccionado explícitamente Si o No
      if (formState.autosoportante === null || formState.autosoportante === undefined) {
        errors.autosoportante = 'Campo obligatorio';
      }
      if (!formState.envase_id) {
        errors.envase_id = 'Campo obligatorio';
      }
      if (!formState.cantidad_cajas_apiladas) {
        errors.cantidad_cajas_apiladas = 'Campo obligatorio';
      }
      // pallet_sobre_pallet es boolean - siempre requerido para estos tipos
      if (formState.pallet_sobre_pallet === null || formState.pallet_sobre_pallet === undefined) {
        errors.pallet_sobre_pallet = 'Campo obligatorio';
      }
      // Cantidad - requerido si pallet_sobre_pallet = true (Si)
      if (formState.pallet_sobre_pallet === true && !formState.cantidad) {
        errors.cantidad = 'Campo obligatorio';
      }
    }

    // Cinta - requerida para tipos 1, 5, 7
    if (tipoSolicitud === 1 || tipoSolicitud === 5 || tipoSolicitud === 7) {
      if (!formState.cascadeData?.cinta) {
        errors.cinta = 'Campo obligatorio';
      }
    }

    // Carton color - requerido si carton_id está vacío
    if (!formState.cascadeData?.cartonId && !formState.cascadeData?.cartonColor) {
      if (esDesarrolloCompletoOCotizanSinCad) {
        errors.carton_color = 'Campo obligatorio';
      }
    }

    // Arte con Material (tipo 5)
    if (esArteConMaterial) {
      if (!formState.reference_type) {
        errors.reference_type = 'Campo obligatorio';
      }
      // reference_id y bloqueo_referencia si reference_type tiene valor y no es 2
      if (formState.reference_type && formState.reference_type !== 2) {
        if (!formState.reference_id) {
          errors.reference_id = 'Campo obligatorio';
        }
        if (!formState.bloqueo_referencia) {
          errors.bloqueo_referencia = 'Campo obligatorio';
        }
      }
    }

    // ========================================
    // CAMPOS CONDICIONALES POR ROL
    // ========================================

    // Vendedor (role 4) o VendedorExterno (role 19) o JefeVenta (role 3)
    if (esVendedor || esVendedorExterno || esJefeVenta) {
      // Org venta - solo vendedor
      if (esVendedor && !formState.org_venta_id) {
        errors.org_venta_id = 'Campo obligatorio';
      }
      // Restriccion pallet - para vendedor, JefeVenta, vendedorExterno y Diseñador en create
      if ((esVendedor || esJefeVenta || esVendedorExterno || esDiseñador) && (esDesarrolloCompletoOCotizanSinCad || esArteConMaterial)) {
        if (formState.restriccion_pallet === null || formState.restriccion_pallet === undefined) {
          errors.restriccion_pallet = 'Campo obligatorio';
        }
      }
      // Termocontraible - solo vendedor
      if (esVendedor && formState.termocontraible === null) {
        errors.termocontraible = 'Campo obligatorio';
      }
      // Armado automatico - solo vendedor
      if (esVendedor && formState.armado_automatico === null) {
        errors.armado_automatico = 'Campo obligatorio';
      }
      // Coverage interno/externo - vendedor y no es muestra
      if ((esVendedor || esVendedorExterno) && noEsMuestra) {
        if (!formState.cascadeData?.coverageInternalId) {
          errors.coverage_internal_id = 'Campo obligatorio';
        }
        if (!formState.cascadeData?.coverageExternalId) {
          errors.coverage_external_id = 'Campo obligatorio';
        }
        // Percentage coverage - cuando coverage_internal_id != 1 (Sin recubrimiento)
        if (formState.cascadeData?.coverageInternalId && formState.cascadeData.coverageInternalId !== 1) {
          if (!formState.percentage_coverage_internal) {
            errors.percentage_coverage_internal = 'Campo obligatorio';
          }
        }
        // Percentage coverage - cuando coverage_external_id != 1 (Sin recubrimiento)
        if (formState.cascadeData?.coverageExternalId && formState.cascadeData.coverageExternalId !== 1) {
          if (!formState.percentage_coverage_external) {
            errors.percentage_coverage_external = 'Campo obligatorio';
          }
        }
      }
      // Planta objetivo - requerido para vendedor, ingeniero y vendedor externo
      if ((esVendedor || esVendedorExterno || esIngeniero) && !formState.cascadeData?.plantaId) {
        errors.planta_id = 'Campo obligatorio';
      }
      // Impresion borde - vendedor, JefeVenta, JefeDesarrollo, Ingeniero
      if ((esVendedor || esJefeVenta || esJefeDesarrollo || esIngeniero) && !formState.impresion_borde) {
        errors.impresion_borde = 'Campo obligatorio';
      }
      // Impresion sobre rayado - vendedor, JefeVenta, JefeDesarrollo, Ingeniero
      if ((esVendedor || esJefeVenta || esJefeDesarrollo || esIngeniero) && !formState.impresion_sobre_rayado) {
        errors.impresion_sobre_rayado = 'Campo obligatorio';
      }
      // Pegado terminacion - vendedor, jefe desarrollo, ingeniero, diseñador en create
      if ((esVendedor || esJefeDesarrollo || esIngeniero || esDiseñador) && !formState.pegado_terminacion) {
        errors.pegado_terminacion = 'Campo obligatorio';
      }
    }

    // JefeDesarrollo o Ingeniero (Dibujante Técnico - role 5, 6)
    if (esIngeniero || esJefeDesarrollo) {
      // Planta objetivo
      if (!formState.cascadeData?.plantaId) {
        errors.planta_id = 'Campo obligatorio';
      }
      // Maquila
      if (!formState.maquila) {
        errors.maquila = 'Campo obligatorio';
      }
      // Impresion borde (ya validado arriba pero asegurar)
      if (!formState.impresion_borde) {
        errors.impresion_borde = 'Campo obligatorio';
      }
      // Impresion sobre rayado (ya validado arriba pero asegurar)
      if (!formState.impresion_sobre_rayado) {
        errors.impresion_sobre_rayado = 'Campo obligatorio';
      }
      // Caracteristicas adicionales - obligatorio para ingeniero y jefe desarrollo
      if (!formState.caracteristicas_adicionales?.trim()) {
        errors.caracteristicas_adicionales = 'Campo obligatorio';
      }
    }

    // JefeDesarrollo - Armado (grupomaterial1)
    if (esJefeDesarrollo && !formState.armado_id) {
      errors.armado_id = 'Campo obligatorio';
    }

    // Carton requerido para muestra o arte con material si es vendedor/vendedorExterno
    if ((esMuestra || esArteConMaterial) && (esVendedor || esVendedorExterno)) {
      if (!formState.cascadeData?.cartonId) {
        errors.carton_id = 'Campo obligatorio';
      }
    }

    // ========================================
    // CAMPOS CONDICIONALES POR OTROS CAMPOS
    // ========================================

    // Pais mercado destino - requerido si FSC = Si (1)
    if (String(formState.cascadeData?.fsc) === '1') {
      if (!formState.pais_id) {
        errors.pais_id = 'Campo obligatorio';
      }
    }

    // Campos de restriccion pallet - requeridos si restriccion_pallet = Si (1)
    if (formState.restriccion_pallet === 1) {
      if (!formState.tamano_pallet_type_id) {
        errors.tamano_pallet_type_id = 'Campo obligatorio';
      }
      if (!formState.altura_pallet) {
        errors.altura_pallet = 'Campo obligatorio';
      }
      if (formState.permite_sobresalir_carga === null) {
        errors.permite_sobresalir_carga = 'Campo obligatorio';
      }
      if (!formState.pallet_qa_id) {
        errors.pallet_qa_id = 'Campo obligatorio';
      }
      // Campos que dependen de la instalación del cliente
      // Solo son obligatorios si hay instalación seleccionada
      if (formState.instalacion_cliente_id) {
        if (formState.bulto_zunchado_pallet === null) {
          errors.bulto_zunchado_pallet = 'Campo obligatorio';
        }
        if (!formState.formato_etiqueta_pallet) {
          errors.formato_etiqueta_pallet = 'Campo obligatorio';
        }
        if (!formState.etiquetas_por_pallet) {
          errors.etiquetas_por_pallet = 'Campo obligatorio';
        }
      }
    }

    // Numero colores - requerido para ciertos tipos e impresiones
    if (noEsMuestra && tipoSolicitud) {
      const impresionVal = Number(formState.cascadeData?.impresion);
      // No requerido si impresion es 5, 6, 7
      if (impresionVal !== 5 && impresionVal !== 6 && impresionVal !== 7) {
        if (!formState.numero_colores && formState.numero_colores !== 0) {
          errors.numero_colores = 'Campo obligatorio';
        }
      }
    }

    // Design type - requerido cuando impresion = 2 (Offset) para tipos 1, 5, 7
    if (esDesarrolloCompletoOCotizanSinCad || esArteConMaterial) {
      const impresionVal = Number(formState.cascadeData?.impresion);
      if (impresionVal === 2 && !formState.design_type_id) {
        errors.design_type_id = 'Campo obligatorio';
      }
    }

    // Product Type Developing y Target Market - requeridos para todos excepto SuperAdministrador
    const esSuperAdministrador = role === ROLES.SuperAdministrador;
    if (!esSuperAdministrador) {
      if (!formState.product_type_developing_id) {
        errors.product_type_developing_id = 'Campo obligatorio';
      }
      if (!formState.target_market_id) {
        errors.target_market_id = 'Campo obligatorio';
      }
    }

    // Issue 18: VB Muestra archivo obligatorio si está marcado
    if (formState.ant_des_vb_muestra && !formState.file_vb_muestra) {
      errors.file_vb_muestra = 'Debe adjuntar archivo para VB Muestra';
    }

    // Issue 19: VB Boceto archivo obligatorio si está marcado
    if (formState.ant_des_vb_boceto && !formState.file_vb_boceto) {
      errors.file_vb_boceto = 'Debe adjuntar archivo para VB Boceto';
    }

    return errors;
  }, [formState]);

  // Limpiar error de campo cuando cambia
  const handleInputChange = useCallback((field: keyof FormState, value: unknown) => {
    setFormState(prev => ({ ...prev, [field]: value }));
    // Limpiar error del campo si existe
    setFieldErrors(prev => {
      if (prev[field]) {
        const { [field]: _, ...rest } = prev;
        return rest;
      }
      return prev;
    });
  }, []);

  const handleSubmit = useCallback((e: React.FormEvent) => {
    e.preventDefault();

    // Validar todos los campos
    const errors = validateForm();
    setFieldErrors(errors);

    // Si hay errores, mostrar mensaje y no enviar
    if (Object.keys(errors).length > 0) {
      const camposConError = Object.keys(errors).length;
      setErrorMessage(`Por favor complete los ${camposConError} campo(s) obligatorio(s) marcados en rojo`);
      // Scroll al primer error
      const firstErrorField = document.querySelector('[data-has-error="true"]');
      if (firstErrorField) {
        firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
      return;
    }

    setErrorMessage(null);

    // Construir datos para enviar (validation ensures required fields are not null)
    const submitData: WorkOrderCreateData = {
      client_id: formState.client_id!,
      descripcion: formState.descripcion,
      tipo_solicitud: formState.tipo_solicitud!,
      canal_id: formState.canal_id!,
      org_venta_id: formState.org_venta_id || undefined,
      nombre_contacto: formState.nombre_contacto || undefined,
      email_contacto: formState.email_contacto || undefined,
      telefono_contacto: formState.telefono_contacto || undefined,
      volumen_venta_anual: formState.volumen_venta_anual || undefined,
      codigo_producto: formState.codigo_producto || undefined,
      oc: formState.oc !== null ? formState.oc : undefined,
      // Jerarquías
      subsubhierarchy_id: formState.subsubhierarchy_id!,
      // Antecedentes Desarrollo - Documentos
      ant_des_correo_cliente: formState.ant_des_correo_cliente ? 1 : 0,
      ant_des_plano_actual: formState.ant_des_plano_actual ? 1 : 0,
      ant_des_boceto_actual: formState.ant_des_boceto_actual ? 1 : 0,
      ant_des_spec: formState.ant_des_spec ? 1 : 0,
      ant_des_otro: formState.ant_des_otro ? 1 : 0,
      // Antecedentes Desarrollo - Muestra Competencia
      ant_des_cj_referencia_de: formState.ant_des_cj_referencia_de ? 1 : 0,
      ant_des_cj_referencia_dg: formState.ant_des_cj_referencia_dg ? 1 : 0,
      ant_des_envase_primario: formState.ant_des_envase_primario ? 1 : 0,
      // Antecedentes Desarrollo - Conservar Muestra
      ant_des_conservar_muestra: formState.ant_des_conservar_muestra === null ? undefined : (formState.ant_des_conservar_muestra ? 1 : 0),
      // Armado Automático
      armado_automatico: formState.armado_automatico === null ? undefined : (formState.armado_automatico ? 1 : 0),
      // Solicita (checkboxes)
      analisis: formState.analisis ? 1 : 0,
      prueba_industrial: formState.prueba_industrial ? 1 : 0,
      muestra: formState.muestra ? 1 : 0,
      numero_muestras: formState.numero_muestras || undefined,
      // Cascade data
      product_type_id: formState.cascadeData?.productTypeId || undefined,
      impresion: formState.cascadeData?.impresion ? Number(formState.cascadeData.impresion) : undefined,
      fsc: formState.cascadeData?.fsc || undefined,
      cinta: formState.cascadeData?.cinta ? Number(formState.cascadeData.cinta) : undefined,
      coverage_internal_id: formState.cascadeData?.coverageInternalId || undefined,
      coverage_external_id: formState.cascadeData?.coverageExternalId || undefined,
      carton_color: formState.cascadeData?.cartonColor ? Number(formState.cascadeData.cartonColor) : undefined,
      carton_id: formState.cascadeData?.cartonId || undefined,
      // Medidas
      interno_largo: formState.interno_largo || undefined,
      interno_ancho: formState.interno_ancho || undefined,
      interno_alto: formState.interno_alto || undefined,
      externo_largo: formState.externo_largo || undefined,
      externo_ancho: formState.externo_ancho || undefined,
      externo_alto: formState.externo_alto || undefined,
      // Terminaciones
      process_id: formState.process_id || undefined,
      armado_id: formState.armado_id || undefined,
      sentido_armado: formState.sentido_armado || undefined,
      // Colores
      numero_colores: formState.numero_colores || undefined,
      color_1_id: formState.color_1_id || undefined,
      color_2_id: formState.color_2_id || undefined,
      color_3_id: formState.color_3_id || undefined,
      color_4_id: formState.color_4_id || undefined,
      color_5_id: formState.color_5_id || undefined,
      // Desarrollo
      peso_contenido_caja: formState.peso_contenido_caja || undefined,
      envase_id: formState.envase_id || undefined,
      autosoportante: formState.autosoportante ? 1 : 0,
      cantidad: formState.cantidad || undefined,
      observacion: formState.observacion || undefined,
      // Planta
      planta_id: formState.planta_id || undefined,
    };

    createMutation.mutate(submitData);
  }, [formState, createMutation]);

  const handleReset = useCallback(() => {
    setFormState(INITIAL_STATE);
    setFieldErrors({}); // Limpiar errores de validación al reiniciar
    setSuccessMessage(null);
    setErrorMessage(null);
  }, []);

  return (
    <Container>
      <Header>
        <Title>Crear Nueva Orden de Trabajo</Title>
        <BackButton onClick={() => onNavigate('dashboard')}>
          ← Volver al Dashboard
        </BackButton>
      </Header>

      {successMessage && <Alert $type="success">{successMessage}</Alert>}
      {errorMessage && <Alert $type="error">{errorMessage}</Alert>}

      <form onSubmit={handleSubmit}>
        {/* Seccion 1: Seleccionar Tipo de Solicitud */}
        <FormSection>
          <SectionHeader>1.- Seleccione el Tipo de Solicitud</SectionHeader>
          <SectionBody>
            <InitialSectionGrid>
              <FormGroup style={{ minWidth: '300px' }}>
                <Label>Tipo de solicitud:</Label>
                <Select
                  id="tipo_solicitud_select"
                  value={formState.tipo_solicitud || ''}
                  onChange={(e) => handleInputChange('tipo_solicitud', e.target.value ? Number(e.target.value) : null)}
                  disabled={!!formState.tipo_solicitud}
                >
                  <option value="">Seleccione tipo...</option>
                  {tipoSolicitudOptions.map(t => (
                    <option key={t.id} value={t.id}>{t.nombre}</option>
                  ))}
                </Select>
              </FormGroup>
              <ResetButton type="button" onClick={handleReset}>
                Reiniciar Solicitud
              </ResetButton>
            </InitialSectionGrid>
          </SectionBody>
        </FormSection>

        {/* Solo mostrar el resto del formulario si se ha seleccionado un tipo */}
        {formState.tipo_solicitud && (
          <>
        {/* Seccion 2: Datos Comerciales - Layout como Laravel */}
        <FormSection>
          <SectionHeader>2.- Datos comerciales</SectionHeader>
          <SectionBody>
            {/* Fila 1: Cliente | Descripción Producto | Código Producto */}
            <FormGrid $columns={3}>
              <FormGroup data-has-error={!!fieldErrors.client_id}>
                <Label style={{ color: fieldErrors.client_id ? '#dc3545' : undefined }}>Cliente *</Label>
                <SearchableSelect
                  options={filterOptions?.clientes || []}
                  value={formState.client_id}
                  onChange={(val) => handleInputChange('client_id', val ? Number(val) : null)}
                  getOptionValue={(c) => c.id}
                  getOptionLabel={(c) => `${c.nombre}${c.codigo ? ` - ${c.codigo}` : ''}`}
                  placeholder="Seleccione cliente..."
                  disabled={optionsLoading}
                  loading={optionsLoading}
                />
                {fieldErrors.client_id && <FieldError>{fieldErrors.client_id}</FieldError>}
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.descripcion}>
                <Label style={{ color: fieldErrors.descripcion ? '#dc3545' : undefined }}>Descripcion del Producto *</Label>
                <Input
                  $hasError={!!fieldErrors.descripcion}
                  type="text"
                  maxLength={40}
                  value={formState.descripcion}
                  onChange={(e) => handleInputChange('descripcion', e.target.value)}
                  placeholder="Max 40 caracteres"
                />
                {fieldErrors.descripcion && <FieldError>{fieldErrors.descripcion}</FieldError>}
              </FormGroup>

              <FormGroup>
                <Label>Codigo Producto</Label>
                <Input
                  type="text"
                  value={formState.codigo_producto}
                  onChange={(e) => handleInputChange('codigo_producto', e.target.value)}
                />
              </FormGroup>
            </FormGrid>

            {/* Fila 2: Tipo Solicitud | Vol Vta Anual | USD | Jerarquía 1 */}
            <FormGrid $columns={4} style={{ marginTop: '1rem' }}>
              <FormGroup>
                <Label>Tipo Solicitud *</Label>
                <Select
                  value={formState.tipo_solicitud || ''}
                  disabled
                >
                  <option value="">Seleccione...</option>
                  {tipoSolicitudOptions.map(t => (
                    <option key={t.id} value={t.id}>{t.nombre}</option>
                  ))}
                </Select>
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.volumen_venta_anual}>
                <Label style={{ color: fieldErrors.volumen_venta_anual ? '#dc3545' : undefined }}>Vol Vta Anual {formState.tipo_solicitud !== 3 ? '*' : ''}</Label>
                <Input
                  $hasError={!!fieldErrors.volumen_venta_anual}
                  type="number"
                  value={formState.volumen_venta_anual || ''}
                  onChange={(e) => handleInputChange('volumen_venta_anual', e.target.value ? Number(e.target.value) : null)}
                />
                {fieldErrors.volumen_venta_anual && <FieldError>{fieldErrors.volumen_venta_anual}</FieldError>}
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.usd_monto}>
                <Label style={{ color: fieldErrors.usd_monto ? '#dc3545' : undefined }}>USD {formState.tipo_solicitud !== 3 ? '*' : ''}</Label>
                <Input
                  $hasError={!!fieldErrors.usd_monto}
                  type="text"
                  value={formState.usd_monto}
                  onChange={(e) => handleInputChange('usd_monto', e.target.value)}
                  placeholder="Monto USD"
                />
              </FormGroup>

              <FormGroup>
                <Label>Jerarquia 1</Label>
                <Select
                  value={formState.hierarchy_id || ''}
                  onChange={(e) => handleInputChange('hierarchy_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={true}
                  style={{ backgroundColor: '#f5f5f5' }}
                  title="Se sincroniza automáticamente con el Canal seleccionado"
                >
                  <option value="">{formState.canal_id ? 'Sincronizado con Canal' : 'Seleccione Canal primero'}</option>
                  {formOptions?.hierarchies.map(h => (
                    <option key={h.id} value={h.id}>{h.nombre}</option>
                  ))}
                </Select>
              </FormGroup>
            </FormGrid>

            {/* Fila 3: Instalación Cliente | Org Venta | Jerarquía 2 */}
            <FormGrid $columns={3} style={{ marginTop: '1rem' }}>
              <FormGroup>
                <Label>Instalacion Cliente</Label>
                <Select
                  value={formState.instalacion_cliente_id || ''}
                  onChange={(e) => handleInputChange('instalacion_cliente_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={!formState.client_id || loadingInstalaciones}
                >
                  <option value="">{loadingInstalaciones ? 'Cargando...' : 'Seleccione...'}</option>
                  {instalaciones.map(inst => (
                    <option key={inst.id} value={inst.id}>{inst.nombre}</option>
                  ))}
                </Select>
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.org_venta_id}>
                <Label style={{ color: fieldErrors.org_venta_id ? '#dc3545' : undefined }}>Org Venta</Label>
                <Select
                  $hasError={!!fieldErrors.org_venta_id}
                  value={formState.org_venta_id || ''}
                  onChange={(e) => handleInputChange('org_venta_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={optionsLoading}
                >
                  <option value="">Seleccione...</option>
                  {filterOptions?.org_ventas?.map(ov => (
                    <option key={ov.id} value={ov.id}>{ov.nombre}</option>
                  ))}
                </Select>
                {fieldErrors.org_venta_id && <FieldError>{fieldErrors.org_venta_id}</FieldError>}
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.subhierarchy_id}>
                <Label style={{ color: fieldErrors.subhierarchy_id ? '#dc3545' : undefined }}>Jerarquia 2 *</Label>
                <Select
                  $hasError={!!fieldErrors.subhierarchy_id}
                  value={formState.subhierarchy_id || ''}
                  onChange={(e) => handleInputChange('subhierarchy_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={!formState.hierarchy_id || formOptionsLoading}
                >
                  <option value="">Seleccione...</option>
                  {filteredSubhierarchies.map(sh => (
                    <option key={sh.id} value={sh.id}>{sh.nombre}</option>
                  ))}
                </Select>
                {fieldErrors.subhierarchy_id && <FieldError>{fieldErrors.subhierarchy_id}</FieldError>}
              </FormGroup>
            </FormGrid>

            {/* Fila 4: Contactos Cliente | Canal | Jerarquía 3 */}
            <FormGrid $columns={3} style={{ marginTop: '1rem' }}>
              <FormGroup>
                <Label>Contactos Cliente</Label>
                <Select
                  value={formState.contactos_cliente_id || ''}
                  onChange={(e) => handleInputChange('contactos_cliente_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={!formState.client_id || loadingContactos}
                >
                  <option value="">{loadingContactos ? 'Cargando...' : 'Seleccionar Opcion'}</option>
                  {contactos.map(cont => (
                    <option key={cont.id} value={cont.id}>{cont.nombre}</option>
                  ))}
                </Select>
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.canal_id}>
                <Label style={{ color: fieldErrors.canal_id ? '#dc3545' : undefined }}>Canal *</Label>
                <Select
                  $hasError={!!fieldErrors.canal_id}
                  value={formState.canal_id || ''}
                  onChange={(e) => handleInputChange('canal_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={optionsLoading}
                >
                  <option value="">Seleccione canal...</option>
                  {filterOptions?.canales.map(c => (
                    <option key={c.id} value={c.id}>{c.nombre}</option>
                  ))}
                </Select>
                {fieldErrors.canal_id && <FieldError>{fieldErrors.canal_id}</FieldError>}
              </FormGroup>

              <FormGroup data-has-error={!!fieldErrors.subsubhierarchy_id}>
                <Label style={{ color: fieldErrors.subsubhierarchy_id ? '#dc3545' : undefined }}>Jerarquia 3 *</Label>
                <Select
                  $hasError={!!fieldErrors.subsubhierarchy_id}
                  value={formState.subsubhierarchy_id || ''}
                  onChange={(e) => handleInputChange('subsubhierarchy_id', e.target.value ? Number(e.target.value) : null)}
                  disabled={!formState.subhierarchy_id || formOptionsLoading}
                >
                  <option value="">Seleccione...</option>
                  {filteredSubsubhierarchies.map(ssh => (
                    <option key={ssh.id} value={ssh.id}>{ssh.nombre}</option>
                  ))}
                </Select>
                {fieldErrors.subsubhierarchy_id && <FieldError>{fieldErrors.subsubhierarchy_id}</FieldError>}
              </FormGroup>
            </FormGrid>

            {/* Fila 5: Nombre Contacto | OC | (vacío) */}
            <FormGrid $columns={3} style={{ marginTop: '1rem' }}>
              <FormGroup data-has-error={!!fieldErrors.nombre_contacto}>
                <Label style={{ color: fieldErrors.nombre_contacto ? '#dc3545' : undefined }}>Nombre Contacto *</Label>
                <Input
                  $hasError={!!fieldErrors.nombre_contacto}
                  type="text"
                  value={formState.nombre_contacto}
                  onChange={(e) => handleInputChange('nombre_contacto', e.target.value)}
                />
                {fieldErrors.nombre_contacto && <FieldError>{fieldErrors.nombre_contacto}</FieldError>}
              </FormGroup>

              {/* Issue 8: OC con opción de adjuntar archivo */}
              <FormGroup>
                <Label>OC</Label>
                <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
                  <Select
                    style={{ flex: 1 }}
                    value={formState.oc === null ? '' : formState.oc}
                    onChange={(e) => handleInputChange('oc', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                  {formState.oc === 1 && (
                    <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                      <label htmlFor="file_oc" style={{ cursor: 'pointer', color: '#28a745' }} title="Adjuntar archivo OC">
                        <span style={{ fontSize: '1.2rem' }}>📎</span>
                      </label>
                      <input
                        type="file"
                        id="file_oc"
                        style={{ display: 'none' }}
                        onChange={(e) => handleInputChange('file_oc', e.target.files?.[0] || null)}
                      />
                      {formState.file_oc && (
                        <span style={{ fontSize: '0.75rem', color: '#28a745', maxWidth: '100px', overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{formState.file_oc.name}</span>
                      )}
                    </div>
                  )}
                </div>
              </FormGroup>

              <FormGroup />
            </FormGrid>

            {/* Fila 6: Email Contacto | (vacío) | (vacío) */}
            <FormGrid $columns={3} style={{ marginTop: '1rem' }}>
              <FormGroup data-has-error={!!fieldErrors.email_contacto}>
                <Label style={{ color: fieldErrors.email_contacto ? '#dc3545' : undefined }}>Email Contacto *</Label>
                <Input
                  $hasError={!!fieldErrors.email_contacto}
                  type="email"
                  value={formState.email_contacto}
                  onChange={(e) => handleInputChange('email_contacto', e.target.value)}
                />
                {fieldErrors.email_contacto && <FieldError>{fieldErrors.email_contacto}</FieldError>}
              </FormGroup>

              <FormGroup />
              <FormGroup />
            </FormGrid>

            {/* Fila 7: Teléfono Contacto | (vacío) | (vacío) */}
            <FormGrid $columns={3} style={{ marginTop: '1rem' }}>
              <FormGroup data-has-error={!!fieldErrors.telefono_contacto}>
                <Label style={{ color: fieldErrors.telefono_contacto ? '#dc3545' : undefined }}>Telefono Contacto *</Label>
                <Input
                  $hasError={!!fieldErrors.telefono_contacto}
                  type="tel"
                  value={formState.telefono_contacto}
                  onChange={(e) => handleInputChange('telefono_contacto', e.target.value)}
                />
                {fieldErrors.telefono_contacto && <FieldError>{fieldErrors.telefono_contacto}</FieldError>}
              </FormGroup>

              <FormGroup />
              <FormGroup />
            </FormGrid>
          </SectionBody>
        </FormSection>

        {/* Fila con Secciones 3, 4 y 5 lado a lado (como en Laravel) */}
        <div style={{ display: 'grid', gridTemplateColumns: '5fr 2fr 5fr', gap: '1rem', marginBottom: '1rem' }}>
          {/* Seccion 3: Antecedentes Desarrollo */}
          <FormSection style={{ marginBottom: 0 }}>
            <SectionHeader>3.- Antecedentes Desarrollo</SectionHeader>
            <SectionBody>
              {/* Issue 11: Documentos con opción de adjuntar archivo */}
              <div style={{ marginBottom: '0.75rem' }}>
                <Label style={{ fontWeight: 'bold', marginBottom: '0.5rem', display: 'block' }}>Documentos:</Label>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                  {/* Correo Cliente */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ minWidth: '120px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_correo_cliente}
                        onChange={(e) => handleInputChange('ant_des_correo_cliente', e.target.checked)}
                      />
                      Correo Cliente
                    </CheckboxLabel>
                    {formState.ant_des_correo_cliente && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_correo" style={{ cursor: 'pointer', color: '#28a745' }} title="Adjuntar archivo">
                          <i className="fa fa-paperclip" style={{ fontSize: '1rem' }}>📎</i>
                        </label>
                        <input
                          type="file"
                          id="file_correo"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_correo_cliente', e.target.files?.[0] || null)}
                        />
                        {formState.file_correo_cliente && (
                          <span style={{ fontSize: '0.75rem', color: '#666' }}>{formState.file_correo_cliente.name}</span>
                        )}
                      </div>
                    )}
                  </div>
                  {/* Plano Actual */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ minWidth: '120px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_plano_actual}
                        onChange={(e) => handleInputChange('ant_des_plano_actual', e.target.checked)}
                      />
                      Plano Actual
                    </CheckboxLabel>
                    {formState.ant_des_plano_actual && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_plano" style={{ cursor: 'pointer', color: '#28a745' }} title="Adjuntar archivo">
                          <i className="fa fa-paperclip" style={{ fontSize: '1rem' }}>📎</i>
                        </label>
                        <input
                          type="file"
                          id="file_plano"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_plano_actual', e.target.files?.[0] || null)}
                        />
                        {formState.file_plano_actual && (
                          <span style={{ fontSize: '0.75rem', color: '#666' }}>{formState.file_plano_actual.name}</span>
                        )}
                      </div>
                    )}
                  </div>
                  {/* Boceto Actual */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ minWidth: '120px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_boceto_actual}
                        onChange={(e) => handleInputChange('ant_des_boceto_actual', e.target.checked)}
                      />
                      Boceto Actual
                    </CheckboxLabel>
                    {formState.ant_des_boceto_actual && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_boceto" style={{ cursor: 'pointer', color: '#28a745' }} title="Adjuntar archivo">
                          <i className="fa fa-paperclip" style={{ fontSize: '1rem' }}>📎</i>
                        </label>
                        <input
                          type="file"
                          id="file_boceto"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_boceto_actual', e.target.files?.[0] || null)}
                        />
                        {formState.file_boceto_actual && (
                          <span style={{ fontSize: '0.75rem', color: '#666' }}>{formState.file_boceto_actual.name}</span>
                        )}
                      </div>
                    )}
                  </div>
                  {/* Spec */}
                  <CheckboxLabel>
                    <input
                      type="checkbox"
                      checked={formState.ant_des_spec}
                      onChange={(e) => handleInputChange('ant_des_spec', e.target.checked)}
                    />
                    Spec
                  </CheckboxLabel>
                  {/* Otro */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ minWidth: '120px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_otro}
                        onChange={(e) => handleInputChange('ant_des_otro', e.target.checked)}
                      />
                      Otro
                    </CheckboxLabel>
                    {formState.ant_des_otro && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_otro" style={{ cursor: 'pointer', color: '#28a745' }} title="Adjuntar archivo">
                          <i className="fa fa-paperclip" style={{ fontSize: '1rem' }}>📎</i>
                        </label>
                        <input
                          type="file"
                          id="file_otro"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_otro', e.target.files?.[0] || null)}
                        />
                        {formState.file_otro && (
                          <span style={{ fontSize: '0.75rem', color: '#666' }}>{formState.file_otro.name}</span>
                        )}
                      </div>
                    )}
                  </div>
                </div>
              </div>

              <hr style={{ margin: '0.75rem 0', borderTop: '1px solid #ddd' }} />

              {/* Muestra Competencia */}
              <div style={{ marginBottom: '0.75rem' }}>
                <Label style={{ fontWeight: 'bold', marginBottom: '0.5rem', display: 'block' }}>Muestra Competencia:</Label>
                <CheckboxGroup>
                  <CheckboxLabel>
                    <input
                      type="checkbox"
                      checked={formState.ant_des_cj_referencia_de}
                      onChange={(e) => handleInputChange('ant_des_cj_referencia_de', e.target.checked)}
                    />
                    CJ Referencia DE
                  </CheckboxLabel>
                  <CheckboxLabel>
                    <input
                      type="checkbox"
                      checked={formState.ant_des_cj_referencia_dg}
                      onChange={(e) => handleInputChange('ant_des_cj_referencia_dg', e.target.checked)}
                    />
                    CJ Referencia DG
                  </CheckboxLabel>
                  <CheckboxLabel>
                    <input
                      type="checkbox"
                      checked={formState.ant_des_envase_primario}
                      onChange={(e) => handleInputChange('ant_des_envase_primario', e.target.checked)}
                    />
                    Envase Primario
                  </CheckboxLabel>
                </CheckboxGroup>
              </div>

              <hr style={{ margin: '0.75rem 0', borderTop: '1px solid #ddd' }} />

              {/* Conservar Muestra y Armado Automático */}
              <div style={{ display: 'flex', gap: '1rem', alignItems: 'flex-start' }}>
                <FormGroup style={{ flex: 1 }}>
                  <Label>Conservar Muestra:</Label>
                  <div style={{ display: 'flex', gap: '1rem', marginTop: '0.25rem' }}>
                    <CheckboxLabel>
                      <input
                        type="radio"
                        name="conservar_muestra"
                        checked={formState.ant_des_conservar_muestra === true}
                        onChange={() => handleInputChange('ant_des_conservar_muestra', true)}
                      />
                      SI
                    </CheckboxLabel>
                    <CheckboxLabel>
                      <input
                        type="radio"
                        name="conservar_muestra"
                        checked={formState.ant_des_conservar_muestra === false}
                        onChange={() => handleInputChange('ant_des_conservar_muestra', false)}
                      />
                      NO
                    </CheckboxLabel>
                  </div>
                </FormGroup>

                <FormGroup style={{ flex: 1 }} data-has-error={!!fieldErrors.armado_automatico}>
                  <Label style={{ color: fieldErrors.armado_automatico ? '#dc3545' : undefined }}>Armado Automatico</Label>
                  <Select
                    $hasError={!!fieldErrors.armado_automatico}
                    value={formState.armado_automatico === null ? '' : formState.armado_automatico ? '1' : '0'}
                    onChange={(e) => handleInputChange('armado_automatico', e.target.value === '' ? null : e.target.value === '1')}
                  >
                    <option value="">Seleccione...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                  {fieldErrors.armado_automatico && <FieldError>{fieldErrors.armado_automatico}</FieldError>}
                </FormGroup>
              </div>
            </SectionBody>
          </FormSection>

          {/* Seccion 4: Solicita */}
          <FormSection style={{ marginBottom: 0 }}>
            <SectionHeader>4.- Solicita</SectionHeader>
            <SectionBody>
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                <CheckboxLabel>
                  <input
                    type="checkbox"
                    checked={formState.analisis}
                    onChange={(e) => handleInputChange('analisis', e.target.checked)}
                  />
                  Analisis
                </CheckboxLabel>
                <CheckboxLabel>
                  <input
                    type="checkbox"
                    checked={formState.prueba_industrial}
                    onChange={(e) => handleInputChange('prueba_industrial', e.target.checked)}
                  />
                  Prueba Industrial
                </CheckboxLabel>
                <CheckboxLabel>
                  <input
                    type="checkbox"
                    checked={formState.muestra}
                    onChange={(e) => {
                      const isChecked = e.target.checked;
                      if (isChecked) {
                        // Abrir modal de Crear Muestra al marcar el checkbox
                        setShowMuestraModal(true);
                      }
                      // Issue 15: No se permite desmarcar el checkbox de Muestra una vez marcado
                      // Se omite el else que permitía desmarcar
                    }}
                    // Issue 15: Si ya está marcado, se deshabilita para prevenir deselección
                    disabled={formState.muestra}
                    style={formState.muestra ? { cursor: 'not-allowed' } : undefined}
                    title={formState.muestra ? 'No se puede deseleccionar una vez marcado' : undefined}
                  />
                  Muestra
                </CheckboxLabel>
              </div>

              {formState.muestra && (
                <FormGroup style={{ marginTop: '1rem' }}>
                  <Label>N Muestras</Label>
                  <Input
                    type="number"
                    min="1"
                    value={formState.numero_muestras || ''}
                    onChange={(e) => handleInputChange('numero_muestras', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100px' }}
                    disabled
                    readOnly
                  />
                </FormGroup>
              )}
            </SectionBody>
          </FormSection>

          {/* Seccion 5: Referencia Material */}
          <FormSection style={{ marginBottom: 0 }}>
            <SectionHeader>5.- Referencia Material</SectionHeader>
            <SectionBody>
              <FormGrid $columns={2}>
                <FormGroup data-has-error={!!fieldErrors.reference_type}>
                  <Label style={{ color: fieldErrors.reference_type ? '#dc3545' : undefined }}>Tipo Referencia</Label>
                  <Select
                    $hasError={!!fieldErrors.reference_type}
                    value={formState.reference_type === null ? '' : formState.reference_type}
                    onChange={(e) => handleInputChange('reference_type', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.reference_types?.map((opt) => (
                      <option key={String(opt.id)} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.reference_type && <FieldError>{fieldErrors.reference_type}</FieldError>}
                </FormGroup>

                <FormGroup data-has-error={!!fieldErrors.reference_id}>
                  <Label style={{ color: fieldErrors.reference_id ? '#dc3545' : undefined }}>Referencia</Label>
                  {/* Issue 17: SearchableSelect con buscador para lista de materiales */}
                  <SearchableSelect
                    options={formOptions?.materials || []}
                    value={formState.reference_id}
                    onChange={(val) => handleInputChange('reference_id', val as number | null)}
                    getOptionValue={(mat) => mat.id}
                    getOptionLabel={(mat) => `${mat.codigo || mat.nombre}${mat.descripcion ? ` - ${mat.descripcion}` : ''}`}
                    placeholder="Buscar referencia..."
                    disabled={!formState.reference_type}
                    loading={formOptionsLoading}
                  />
                  {fieldErrors.reference_id && <FieldError>{fieldErrors.reference_id}</FieldError>}
                </FormGroup>

                <FormGroup data-has-error={!!fieldErrors.bloqueo_referencia}>
                  <Label style={{ color: fieldErrors.bloqueo_referencia ? '#dc3545' : undefined }}>Bloqueo Referencia</Label>
                  <Select
                    $hasError={!!fieldErrors.bloqueo_referencia}
                    value={formState.bloqueo_referencia === null ? '' : formState.bloqueo_referencia}
                    onChange={(e) => handleInputChange('bloqueo_referencia', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.bloqueo_referencia?.map((opt) => (
                      <option key={String(opt.id)} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.bloqueo_referencia && <FieldError>{fieldErrors.bloqueo_referencia}</FieldError>}
                </FormGroup>

                <FormGroup>
                  <Label>Indicador Facturacion D.E.</Label>
                  <Select
                    value={formState.indicador_facturacion || ''}
                    onChange={(e) => handleInputChange('indicador_facturacion', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.indicador_facturacion?.map((opt) => (
                      <option key={String(opt.id)} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
              </FormGrid>

              {/* Issue 18, 19: Archivo Visto Bueno con upload */}
              <div style={{ marginTop: '1rem', padding: '0.75rem', background: '#f8f9fa', borderRadius: '4px' }}>
                <Label style={{ fontWeight: 'bold', marginBottom: '0.5rem', display: 'block' }}>Archivo Visto Bueno</Label>
                <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                  {/* Issue 18: VB Muestra con archivo obligatorio */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ color: '#28a745', fontWeight: 'bold', minWidth: '100px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_vb_muestra}
                        onChange={(e) => handleInputChange('ant_des_vb_muestra', e.target.checked)}
                      />
                      VB Muestra
                    </CheckboxLabel>
                    {formState.ant_des_vb_muestra && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_vb_muestra" style={{ cursor: 'pointer', color: formState.file_vb_muestra ? '#28a745' : '#dc3545' }} title="Adjuntar archivo (obligatorio)">
                          <span style={{ fontSize: '1rem' }}>📎</span>
                        </label>
                        <input
                          type="file"
                          id="file_vb_muestra"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_vb_muestra', e.target.files?.[0] || null)}
                        />
                        {formState.file_vb_muestra ? (
                          <span style={{ fontSize: '0.75rem', color: '#28a745' }}>{formState.file_vb_muestra.name}</span>
                        ) : (
                          <span style={{ fontSize: '0.75rem', color: '#dc3545' }}>* Obligatorio</span>
                        )}
                      </div>
                    )}
                  </div>
                  {/* Issue 19: VB Boceto con archivo obligatorio */}
                  <div style={{ display: 'flex', alignItems: 'center', gap: '0.5rem' }}>
                    <CheckboxLabel style={{ color: '#28a745', fontWeight: 'bold', minWidth: '100px' }}>
                      <input
                        type="checkbox"
                        checked={formState.ant_des_vb_boceto}
                        onChange={(e) => handleInputChange('ant_des_vb_boceto', e.target.checked)}
                      />
                      VB Boceto
                    </CheckboxLabel>
                    {formState.ant_des_vb_boceto && (
                      <div style={{ display: 'flex', alignItems: 'center', gap: '0.25rem' }}>
                        <label htmlFor="file_vb_boceto" style={{ cursor: 'pointer', color: formState.file_vb_boceto ? '#28a745' : '#dc3545' }} title="Adjuntar archivo (obligatorio)">
                          <span style={{ fontSize: '1rem' }}>📎</span>
                        </label>
                        <input
                          type="file"
                          id="file_vb_boceto"
                          style={{ display: 'none' }}
                          onChange={(e) => handleInputChange('file_vb_boceto', e.target.files?.[0] || null)}
                        />
                        {formState.file_vb_boceto ? (
                          <span style={{ fontSize: '0.75rem', color: '#28a745' }}>{formState.file_vb_boceto.name}</span>
                        ) : (
                          <span style={{ fontSize: '0.75rem', color: '#dc3545' }}>* Obligatorio</span>
                        )}
                      </div>
                    )}
                  </div>
                </div>
              </div>
            </SectionBody>
          </FormSection>
        </div>

        {/* Seccion 6: Asistente Para Ingresos Principales */}
        <FormSection>
          <SectionHeader>6.- Asistente Para: Ingresos Principales</SectionHeader>
          <SectionBody>
            <CascadeForm
              values={formState.cascadeData}
              onChange={handleCascadeChange}
              fieldErrors={fieldErrors}
            />
          </SectionBody>
        </FormSection>

        {/* Seccion 7: Caracteristicas */}
        <FormSection>
          <SectionHeader>7.- Caracteristicas</SectionHeader>
          <SectionBody>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr 1fr', gap: '1.5rem' }}>
              {/* Columna 1 */}
              <div>
                <FormGroup>
                  <Label>CAD</Label>
                  {/* Issue 26: Al seleccionar CAD, carga datos automáticamente */}
                  <Select
                    value={formState.cad_id || ''}
                    onChange={(e) => handleCADChange(e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.cads?.map((cad) => (
                      <option key={String(cad.id)} value={cad.id}>{cad.codigo || cad.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>Matriz</Label>
                  <Select
                    value={formState.matriz_id || ''}
                    onChange={(e) => handleInputChange('matriz_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.matrices?.map((m) => (
                      <option key={m.id} value={m.id}>{m.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>Tipo Matriz</Label>
                  <Input
                    type="text"
                    value={formState.tipo_matriz_text}
                    onChange={(e) => handleInputChange('tipo_matriz_text', e.target.value)}
                    disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                    style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                  />
                </FormGroup>
                {/* Campos sincronizados de Seccion 6 */}
                <FormGroup>
                  <Label>Tipo Item</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.productTypeId ?
                      (formOptions?.product_types?.find(p => p.id === formState.cascadeData.productTypeId)?.nombre || '') : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>Items del Set</Label>
                    <Input
                      type="number"
                      min="0"
                      value={formState.items_set || ''}
                      onChange={(e) => handleInputChange('items_set', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>Veces Item</Label>
                    <Input
                      type="number"
                      min="0"
                      value={formState.veces_item || ''}
                      onChange={(e) => handleInputChange('veces_item', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                {/* Mas campos sincronizados de Seccion 6 */}
                <FormGroup>
                  <Label>Color Carton</Label>
                  <Input
                    type="text"
                    value={String(formState.cascadeData.cartonColor) === '1' ? 'Café' : String(formState.cascadeData.cartonColor) === '2' ? 'Blanco' : formState.cascadeData.cartonColor || ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Carton</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.cartonId ?
                      (formOptions?.cartons?.find(c => c.id === formState.cascadeData.cartonId)?.nombre || '') : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Cinta</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.cinta !== null && formState.cascadeData.cinta !== undefined
                      ? (Number(formState.cascadeData.cinta) === 1 ? 'Si' : 'No')
                      : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>FSC</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.fsc ?
                      (formOptions?.fsc?.find(f => f.id === Number(formState.cascadeData.fsc))?.nombre || String(formState.cascadeData.fsc)) : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.pallet_qa_id}>
                  <Label style={{ color: fieldErrors.pallet_qa_id ? '#dc3545' : undefined }}>Certificado Calidad</Label>
                  <Select
                    $hasError={!!fieldErrors.pallet_qa_id}
                    value={formState.pallet_qa_id || ''}
                    onChange={(e) => handleInputChange('pallet_qa_id', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.pallet_qas?.map((p) => (
                      <option key={p.id} value={p.id}>{p.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.pallet_qa_id && <FieldError>{fieldErrors.pallet_qa_id}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.pais_id}>
                  <Label style={{ color: fieldErrors.pais_id ? '#dc3545' : undefined }}>Pais/Mercado Destino</Label>
                  <Select
                    $hasError={!!fieldErrors.pais_id}
                    value={formState.pais_id || ''}
                    onChange={(e) => handleInputChange('pais_id', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.pais_referencia?.map((p) => (
                      <option key={String(p.id)} value={p.id}>{p.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.pais_id && <FieldError>{fieldErrors.pais_id}</FieldError>}
                </FormGroup>
                <FormGroup>
                  <Label>Planta Objetivo</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.plantaId ?
                      (formOptions?.plantas?.find(p => p.id === formState.cascadeData.plantaId)?.nombre || '') : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.restriccion_pallet}>
                  <Label style={{ color: fieldErrors.restriccion_pallet ? '#dc3545' : undefined }}>Restriccion Paletizado</Label>
                  <Select
                    $hasError={!!fieldErrors.restriccion_pallet}
                    value={formState.restriccion_pallet === null ? '' : formState.restriccion_pallet}
                    onChange={(e) => handleInputChange('restriccion_pallet', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                  {fieldErrors.restriccion_pallet && <FieldError>{fieldErrors.restriccion_pallet}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.tamano_pallet_type_id}>
                  <Label style={{ color: fieldErrors.tamano_pallet_type_id ? '#dc3545' : undefined }}>Tamano Pallet</Label>
                  <Select
                    $hasError={!!fieldErrors.tamano_pallet_type_id}
                    value={formState.tamano_pallet_type_id || ''}
                    onChange={(e) => handleInputChange('tamano_pallet_type_id', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.pallet_types?.map((p) => (
                      <option key={String(p.id)} value={p.id}>{p.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.tamano_pallet_type_id && <FieldError>{fieldErrors.tamano_pallet_type_id}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.altura_pallet}>
                  <Label style={{ color: fieldErrors.altura_pallet ? '#dc3545' : undefined }}>Altura Pallet</Label>
                  <Input
                    $hasError={!!fieldErrors.altura_pallet}
                    type="number"
                    min="0"
                    value={formState.altura_pallet || ''}
                    onChange={(e) => handleInputChange('altura_pallet', e.target.value ? Number(e.target.value) : null)}
                  />
                  {fieldErrors.altura_pallet && <FieldError>{fieldErrors.altura_pallet}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.permite_sobresalir_carga}>
                  <Label style={{ color: fieldErrors.permite_sobresalir_carga ? '#dc3545' : undefined }}>Permite Sobresalir Carga</Label>
                  <Select
                    $hasError={!!fieldErrors.permite_sobresalir_carga}
                    value={formState.permite_sobresalir_carga === null ? '' : formState.permite_sobresalir_carga}
                    onChange={(e) => handleInputChange('permite_sobresalir_carga', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                  {fieldErrors.permite_sobresalir_carga && <FieldError>{fieldErrors.permite_sobresalir_carga}</FieldError>}
                </FormGroup>
              </div>

              {/* Columna 2 */}
              <div>
                <FormGroup>
                  <Label>Estilo</Label>
                  <Select
                    value={formState.style_id || ''}
                    onChange={(e) => handleInputChange('style_id', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.styles?.map((s) => (
                      <option key={String(s.id)} value={s.id}>{s.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.caracteristicas_adicionales}>
                  <Label style={{ color: fieldErrors.caracteristicas_adicionales ? '#dc3545' : undefined }}>Caracteristica Estilo</Label>
                  <Input
                    $hasError={!!fieldErrors.caracteristicas_adicionales}
                    type="text"
                    value={formState.caracteristicas_adicionales}
                    onChange={(e) => handleInputChange('caracteristicas_adicionales', e.target.value)}
                    disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                    style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                  />
                  {fieldErrors.caracteristicas_adicionales && <FieldError>{fieldErrors.caracteristicas_adicionales}</FieldError>}
                </FormGroup>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>Largura HM</Label>
                    <Input
                      type="number"
                      value={formState.largura_hm || ''}
                      onChange={(e) => handleInputChange('largura_hm', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>Anchura HM</Label>
                    <Input
                      type="number"
                      value={formState.anchura_hm || ''}
                      onChange={(e) => handleInputChange('anchura_hm', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <FormGroup>
                  <Label>Area Producto (M2)</Label>
                  <Input
                    type="number"
                    value={formState.area_producto_m2 || ''}
                    onChange={(e) => handleInputChange('area_producto_m2', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Recorte Adicional / Area Agujero (M2)</Label>
                  <Input
                    type="number"
                    value={formState.recorte_adicional_m2 || ''}
                    onChange={(e) => handleInputChange('recorte_adicional_m2', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Longitud Pegado (MM)</Label>
                  <Input
                    type="number"
                    value={formState.longitud_pegado_mm || ''}
                    onChange={(e) => handleInputChange('longitud_pegado_mm', e.target.value ? Number(e.target.value) : null)}
                    disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                    style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                  />
                </FormGroup>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>Golpes al Largo</Label>
                    <Input
                      type="number"
                      value={formState.golpes_largo || ''}
                      onChange={(e) => handleInputChange('golpes_largo', e.target.value ? Number(e.target.value) : null)}
                      disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                      style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>Golpes al Ancho</Label>
                    <Input
                      type="number"
                      value={formState.golpes_ancho || ''}
                      onChange={(e) => handleInputChange('golpes_ancho', e.target.value ? Number(e.target.value) : null)}
                      disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                      style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                    />
                  </FormGroup>
                </div>
                <FormGroup>
                  <Label>Separacion Golpes Largo (MM)</Label>
                  <Input
                    type="number"
                    value={formState.separacion_golpes_largo || ''}
                    onChange={(e) => handleInputChange('separacion_golpes_largo', e.target.value ? Number(e.target.value) : null)}
                    placeholder="0"
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Separacion Golpes Ancho (MM)</Label>
                  <Input
                    type="number"
                    value={formState.separacion_golpes_ancho || ''}
                    onChange={(e) => handleInputChange('separacion_golpes_ancho', e.target.value ? Number(e.target.value) : null)}
                    placeholder="0"
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Cuchillas (ML)</Label>
                  <Input
                    type="number"
                    value={formState.cuchillas_ml || ''}
                    onChange={(e) => handleInputChange('cuchillas_ml', e.target.value ? Number(e.target.value) : null)}
                    disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                    style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                  />
                </FormGroup>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>Rayado C1/R1 (MM)</Label>
                    <Input
                      type="number"
                      value={formState.rayado_c1_r1 || ''}
                      onChange={(e) => handleInputChange('rayado_c1_r1', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>Rayado R1/R2 (MM)</Label>
                    <Input
                      type="number"
                      value={formState.rayado_r1_r2 || ''}
                      onChange={(e) => handleInputChange('rayado_r1_r2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <FormGroup>
                  <Label>Rayado R2/C2 (MM)</Label>
                  <Input
                    type="number"
                    value={formState.rayado_r2_c2 || ''}
                    onChange={(e) => handleInputChange('rayado_r2_c2', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.bulto_zunchado_pallet}>
                  <Label style={{ color: fieldErrors.bulto_zunchado_pallet ? '#dc3545' : undefined }}>Bulto Zunchado al Pallet</Label>
                  <Select
                    $hasError={!!fieldErrors.bulto_zunchado_pallet}
                    value={formState.bulto_zunchado_pallet === null ? '' : formState.bulto_zunchado_pallet}
                    onChange={(e) => handleInputChange('bulto_zunchado_pallet', e.target.value === '' ? null : Number(e.target.value))}
                    disabled={!formState.instalacion_cliente_id}
                    style={!formState.instalacion_cliente_id ? { backgroundColor: '#f5f5f5' } : undefined}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                  {fieldErrors.bulto_zunchado_pallet && <FieldError>{fieldErrors.bulto_zunchado_pallet}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.formato_etiqueta_pallet}>
                  <Label style={{ color: fieldErrors.formato_etiqueta_pallet ? '#dc3545' : undefined }}>Formato Etiqueta Pallet</Label>
                  <Select
                    $hasError={!!fieldErrors.formato_etiqueta_pallet}
                    value={formState.formato_etiqueta_pallet || ''}
                    onChange={(e) => handleInputChange('formato_etiqueta_pallet', e.target.value ? Number(e.target.value) : null)}
                    disabled={!formState.instalacion_cliente_id}
                    style={!formState.instalacion_cliente_id ? { backgroundColor: '#f5f5f5' } : undefined}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.pallet_tag_formats?.map((p) => (
                      <option key={p.id} value={p.id}>{p.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.formato_etiqueta_pallet && <FieldError>{fieldErrors.formato_etiqueta_pallet}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.etiquetas_por_pallet}>
                  <Label style={{ color: fieldErrors.etiquetas_por_pallet ? '#dc3545' : undefined }}>N Etiquetas por Pallet</Label>
                  <Input
                    $hasError={!!fieldErrors.etiquetas_por_pallet}
                    type="number"
                    value={formState.etiquetas_por_pallet || ''}
                    onChange={(e) => handleInputChange('etiquetas_por_pallet', e.target.value ? Number(e.target.value) : null)}
                    disabled={!formState.instalacion_cliente_id}
                    style={!formState.instalacion_cliente_id ? { backgroundColor: '#f5f5f5' } : undefined}
                  />
                  {fieldErrors.etiquetas_por_pallet && <FieldError>{fieldErrors.etiquetas_por_pallet}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.termocontraible}>
                  <Label style={{ color: fieldErrors.termocontraible ? '#dc3545' : undefined }}>Termocontraible</Label>
                  <Select
                    $hasError={!!fieldErrors.termocontraible}
                    value={formState.termocontraible === null ? '' : formState.termocontraible}
                    onChange={(e) => handleInputChange('termocontraible', e.target.value === '' ? null : Number(e.target.value))}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="1">Si</option>
                    <option value="0">No</option>
                  </Select>
                </FormGroup>
              </div>

              {/* Columna 3: Especificaciones Tecnicas */}
              <div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>BCT MIN (LB)</Label>
                    <Input
                      type="number"
                      value={formState.bct_min_lb || ''}
                      onChange={(e) => handleInputChange('bct_min_lb', e.target.value ? Number(e.target.value) : null)}
                      disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                      style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>BCT MIN (KG)</Label>
                    <Input
                      type="number"
                      value={formState.bct_min_kg || ''}
                      onChange={(e) => handleInputChange('bct_min_kg', e.target.value ? Number(e.target.value) : null)}
                      disabled={getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno}
                      style={(getCurrentUserRole() === ROLES.Vendedor || getCurrentUserRole() === ROLES.VendedorExterno) ? { backgroundColor: '#f5f5f5' } : undefined}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>BCT HUMEDO (LB)</Label>
                    <Input
                      type="number"
                      value={formState.bct_humedo_lb || ''}
                      onChange={(e) => handleInputChange('bct_humedo_lb', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>ECT MIN (LB/PULG)</Label>
                    <Input
                      type="number"
                      step="0.01"
                      value={formState.ect_min_lb_pulg || ''}
                      onChange={(e) => handleInputChange('ect_min_lb_pulg', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>GRAMAJE (G/M2)</Label>
                    <Input
                      type="number"
                      value={formState.gramaje_g_m2 || ''}
                      onChange={(e) => handleInputChange('gramaje_g_m2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>MULLEN (LB/PULG2)</Label>
                    <Input
                      type="number"
                      value={formState.mullen_lb_pulg2 || ''}
                      onChange={(e) => handleInputChange('mullen_lb_pulg2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>FCT (LB/PULG2)</Label>
                    <Input
                      type="number"
                      step="0.01"
                      value={formState.fct_lb_pulg2 || ''}
                      onChange={(e) => handleInputChange('fct_lb_pulg2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>DST (BPI)</Label>
                    <Input
                      type="number"
                      value={formState.dst_bpi || ''}
                      onChange={(e) => handleInputChange('dst_bpi', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>ESPESOR PLACA (MM)</Label>
                    <Input
                      type="number"
                      value={formState.espesor_placa_mm || ''}
                      onChange={(e) => handleInputChange('espesor_placa_mm', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>ESPESOR CAJA (MM)</Label>
                    <Input
                      type="number"
                      value={formState.espesor_caja_mm || ''}
                      onChange={(e) => handleInputChange('espesor_caja_mm', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>COBB INTERIOR (G/M2)</Label>
                    <Input
                      type="number"
                      value={formState.cobb_interior_g_m2 || ''}
                      onChange={(e) => handleInputChange('cobb_interior_g_m2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>COBB EXTERIOR (G/M2)</Label>
                    <Input
                      type="number"
                      value={formState.cobb_exterior_g_m2 || ''}
                      onChange={(e) => handleInputChange('cobb_exterior_g_m2', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>FLEXION DE ALETA (N)</Label>
                    <Input
                      type="number"
                      value={formState.flexion_aleta_n || ''}
                      onChange={(e) => handleInputChange('flexion_aleta_n', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>PESO CLIENTE (G)</Label>
                    <Input
                      type="number"
                      value={formState.peso_cliente_g || ''}
                      onChange={(e) => handleInputChange('peso_cliente_g', e.target.value ? Number(e.target.value) : null)}
                    />
                  </FormGroup>
                </div>
                <FormGroup>
                  <Label>Incision Rayado Longitudinal (N)</Label>
                  <Input
                    type="text"
                    value={formState.incision_rayado_longitudinal}
                    onChange={(e) => handleInputChange('incision_rayado_longitudinal', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Incision Rayado Transversal (N)</Label>
                  <Input
                    type="text"
                    value={formState.incision_rayado_transversal}
                    onChange={(e) => handleInputChange('incision_rayado_transversal', e.target.value)}
                  />
                </FormGroup>
                <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '0.5rem' }}>
                  <FormGroup>
                    <Label>Porosidad (SEG)</Label>
                    <Input
                      type="text"
                      value={formState.porosidad}
                      onChange={(e) => handleInputChange('porosidad', e.target.value)}
                    />
                  </FormGroup>
                  <FormGroup>
                    <Label>Brillo (%)</Label>
                    <Input
                      type="text"
                      value={formState.brillo}
                      onChange={(e) => handleInputChange('brillo', e.target.value)}
                    />
                  </FormGroup>
                </div>
                <FormGroup>
                  <Label>Rigidez 4 Puntos Longitudinal (N/mm)</Label>
                  <Input
                    type="text"
                    value={formState.rigidez_4_ptos_long}
                    onChange={(e) => handleInputChange('rigidez_4_ptos_long', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Rigidez 4 Puntos Transversal (N/mm)</Label>
                  <Input
                    type="text"
                    value={formState.rigidez_4_ptos_transv}
                    onChange={(e) => handleInputChange('rigidez_4_ptos_transv', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Angulo Deslizamiento-Tapa Exterior</Label>
                  <Input
                    type="text"
                    value={formState.angulo_deslizamiento_exterior}
                    onChange={(e) => handleInputChange('angulo_deslizamiento_exterior', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Angulo Deslizamiento-Tapa Interior</Label>
                  <Input
                    type="text"
                    value={formState.angulo_deslizamiento_interior}
                    onChange={(e) => handleInputChange('angulo_deslizamiento_interior', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Resistencia al Frote</Label>
                  <Input
                    type="text"
                    value={formState.resistencia_frote}
                    onChange={(e) => handleInputChange('resistencia_frote', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Contenido Reciclado (%)</Label>
                  <Input
                    type="text"
                    value={formState.contenido_reciclado}
                    onChange={(e) => handleInputChange('contenido_reciclado', e.target.value)}
                  />
                </FormGroup>
              </div>
            </div>
          </SectionBody>
        </FormSection>

        {/* Issue 42: Seccion Distancia Cinta - Solo visible cuando Cinta = SI (1) */}
        {Number(formState.cascadeData.cinta) === 1 && (
          <FormSection>
            <SectionHeader>Distancia Cinta</SectionHeader>
            <SectionBody>
              <FormGrid>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 1 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_1 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_1', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 2 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_2 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_2', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 3 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_3 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_3', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 4 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_4 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_4', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 5 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_5 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_5', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Distancia Corte 1 a Cinta 6 (mm)</Label>
                  <Input
                    type="number"
                    min="0"
                    value={formState.distancia_cinta_6 || ''}
                    onChange={(e) => handleInputChange('distancia_cinta_6', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
              </FormGrid>
            </SectionBody>
          </FormSection>
        )}

        {/* Seccion 8: Color-Cera-Barniz */}
        <FormSection>
          <SectionHeader>8.- Color-Cera-Barniz</SectionHeader>
          <SectionBody>
            <FormGridSection8>
              {/* Columna 1: Impresión, Trazabilidad, Tipo Diseño, etc. */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                <FormGroup>
                  <Label>Impresión</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.impresion ?
                      (formOptions?.impresiones?.find(i => String(i.id) === String(formState.cascadeData.impresion))?.nombre || formState.cascadeData.impresion) : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Trazabilidad</Label>
                  <Select
                    value={formState.trazabilidad || ''}
                    onChange={(e) => handleInputChange('trazabilidad', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.trazabilidad?.map(t => (
                      <option key={t.id} value={t.id}>{t.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.design_type_id}>
                  <Label style={{ color: fieldErrors.design_type_id ? '#dc3545' : undefined }}>Tipo Diseño</Label>
                  <Select
                    $hasError={!!fieldErrors.design_type_id}
                    value={formState.design_type_id || ''}
                    onChange={(e) => handleInputChange('design_type_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.design_types?.map(dt => (
                      <option key={dt.id} value={dt.id}>{dt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.design_type_id && <FieldError>{fieldErrors.design_type_id}</FieldError>}
                </FormGroup>
                <FormGroup>
                  <Label>Complejidad</Label>
                  <Input
                    type="text"
                    value={formState.complejidad}
                    onChange={(e) => handleInputChange('complejidad', e.target.value)}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.numero_colores}>
                  <Label style={{ color: fieldErrors.numero_colores ? '#dc3545' : undefined }}>Número Colores</Label>
                  <Select
                    $hasError={!!fieldErrors.numero_colores}
                    value={formState.numero_colores !== null ? formState.numero_colores : ''}
                    onChange={(e) => handleInputChange('numero_colores', e.target.value !== '' ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccione...</option>
                    {[0,1,2,3,4,5,6,7].map(n => (
                      <option key={n} value={n}>{n}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>Recubrimiento Interno</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.coverageInternalId ?
                      (formOptions?.coverages_internal?.find(c => c.id === formState.cascadeData.coverageInternalId)?.nombre || '') : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.percentage_coverage_internal}>
                  <Label style={{ color: fieldErrors.percentage_coverage_internal ? '#dc3545' : undefined }}>% Recubrimiento Interno</Label>
                  <Input
                    $hasError={!!fieldErrors.percentage_coverage_internal}
                    type="number"
                    min="0"
                    value={formState.percentage_coverage_internal || ''}
                    onChange={(e) => handleInputChange('percentage_coverage_internal', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Recubrimiento Externo</Label>
                  <Input
                    type="text"
                    value={formState.cascadeData.coverageExternalId ?
                      (formOptions?.coverages_external?.find(c => c.id === formState.cascadeData.coverageExternalId)?.nombre || '') : ''}
                    readOnly
                    style={{ backgroundColor: '#f5f5f5' }}
                  />
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.percentage_coverage_external}>
                  <Label style={{ color: fieldErrors.percentage_coverage_external ? '#dc3545' : undefined }}>% Recubrimiento Externo</Label>
                  <Input
                    $hasError={!!fieldErrors.percentage_coverage_external}
                    type="number"
                    min="0"
                    value={formState.percentage_coverage_external || ''}
                    onChange={(e) => handleInputChange('percentage_coverage_external', e.target.value ? Number(e.target.value) : null)}
                  />
                </FormGroup>
              </div>

              {/* Columna 2: Colores 1-4 */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                <FormGroup>
                  <Label>Color 1 (Interior TyR)</Label>
                  <Select
                    value={formState.color_1_id || ''}
                    onChange={(e) => handleInputChange('color_1_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 1}
                    style={formState.numero_colores !== null && formState.numero_colores >= 1 ? {} : { backgroundColor: '#e9ecef' }}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (
                      <option key={c.id} value={c.id}>{c.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 1</Label>
                  <Input type="number" min="0" value={formState.impresion_1 || ''} onChange={(e) => handleInputChange('impresion_1', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 1} style={formState.numero_colores !== null && formState.numero_colores >= 1 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 1</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_1 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_1', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 1} style={formState.numero_colores !== null && formState.numero_colores >= 1 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Color 2</Label>
                  <Select value={formState.color_2_id || ''} onChange={(e) => handleInputChange('color_2_id', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 2} style={formState.numero_colores !== null && formState.numero_colores >= 2 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 2</Label>
                  <Input type="number" min="0" value={formState.impresion_2 || ''} onChange={(e) => handleInputChange('impresion_2', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 2} style={formState.numero_colores !== null && formState.numero_colores >= 2 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 2</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_2 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_2', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 2} style={formState.numero_colores !== null && formState.numero_colores >= 2 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Color 3</Label>
                  <Select value={formState.color_3_id || ''} onChange={(e) => handleInputChange('color_3_id', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 3} style={formState.numero_colores !== null && formState.numero_colores >= 3 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 3</Label>
                  <Input type="number" min="0" value={formState.impresion_3 || ''} onChange={(e) => handleInputChange('impresion_3', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 3} style={formState.numero_colores !== null && formState.numero_colores >= 3 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 3</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_3 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_3', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 3} style={formState.numero_colores !== null && formState.numero_colores >= 3 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Color 4</Label>
                  <Select value={formState.color_4_id || ''} onChange={(e) => handleInputChange('color_4_id', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 4} style={formState.numero_colores !== null && formState.numero_colores >= 4 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 4</Label>
                  <Input type="number" min="0" value={formState.impresion_4 || ''} onChange={(e) => handleInputChange('impresion_4', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 4} style={formState.numero_colores !== null && formState.numero_colores >= 4 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 4</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_4 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_4', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 4} style={formState.numero_colores !== null && formState.numero_colores >= 4 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
              </div>

              {/* Columna 3: Colores 5-7 (Barniz UV) */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                <FormGroup>
                  <Label>Color 5</Label>
                  <Select value={formState.color_5_id || ''} onChange={(e) => handleInputChange('color_5_id', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 5} style={formState.numero_colores !== null && formState.numero_colores >= 5 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 5</Label>
                  <Input type="number" min="0" value={formState.impresion_5 || ''} onChange={(e) => handleInputChange('impresion_5', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 5} style={formState.numero_colores !== null && formState.numero_colores >= 5 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 5</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_5 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_5', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 5} style={formState.numero_colores !== null && formState.numero_colores >= 5 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Color 6</Label>
                  <Select value={formState.color_6_id || ''} onChange={(e) => handleInputChange('color_6_id', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 6} style={formState.numero_colores !== null && formState.numero_colores >= 6 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 6</Label>
                  <Input type="number" min="0" value={formState.impresion_6 || ''} onChange={(e) => handleInputChange('impresion_6', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 6} style={formState.numero_colores !== null && formState.numero_colores >= 6 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 6</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_6 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_6', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 6} style={formState.numero_colores !== null && formState.numero_colores >= 6 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Color 7 (Barniz UV)</Label>
                  <Select value={formState.barniz_uv || ''} onChange={(e) => handleInputChange('barniz_uv', e.target.value ? Number(e.target.value) : null)} disabled={formOptionsLoading || formState.numero_colores === null || formState.numero_colores < 7} style={formState.numero_colores !== null && formState.numero_colores >= 7 ? {} : { backgroundColor: '#e9ecef' }}>
                    <option value="">Seleccione...</option>
                    {formOptions?.colors?.map(c => (<option key={c.id} value={c.id}>{c.nombre}</option>))}
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>% Impresión 7 UV</Label>
                  <Input type="number" min="0" value={formState.porcentaje_barniz_uv || ''} onChange={(e) => handleInputChange('porcentaje_barniz_uv', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 7} style={formState.numero_colores !== null && formState.numero_colores >= 7 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
                <FormGroup>
                  <Label>Clisse cm2 7</Label>
                  <Input type="number" min="0" value={formState.cm2_clisse_color_7 || ''} onChange={(e) => handleInputChange('cm2_clisse_color_7', e.target.value ? Number(e.target.value) : null)} disabled={formState.numero_colores === null || formState.numero_colores < 7} style={formState.numero_colores !== null && formState.numero_colores >= 7 ? {} : { backgroundColor: '#e9ecef' }} />
                </FormGroup>
              </div>

              {/* Columna 4: Opciones adicionales */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.5rem' }}>
                <FormGroup>
                  <Label>Indicador Facturación D.G.</Label>
                  <Input
                    type="text"
                    value={formState.indicador_facturacion_diseno_grafico}
                    onChange={(e) => handleInputChange('indicador_facturacion_diseno_grafico', e.target.value)}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Prueba de Color</Label>
                  <Select
                    value={formState.prueba_color !== null ? formState.prueba_color : ''}
                    onChange={(e) => handleInputChange('prueba_color', e.target.value !== '' ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccione...</option>
                    <option value={1}>Sí</option>
                    <option value={0}>No</option>
                  </Select>
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.impresion_borde}>
                  <Label style={{ color: fieldErrors.impresion_borde ? '#dc3545' : undefined }}>Impresión de Borde</Label>
                  <Select
                    $hasError={!!fieldErrors.impresion_borde}
                    value={formState.impresion_borde}
                    onChange={(e) => handleInputChange('impresion_borde', e.target.value)}
                  >
                    <option value="">Seleccione...</option>
                    <option value="SI">Sí</option>
                    <option value="NO">No</option>
                  </Select>
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.impresion_sobre_rayado}>
                  <Label style={{ color: fieldErrors.impresion_sobre_rayado ? '#dc3545' : undefined }}>Impresión Sobre Rayado</Label>
                  <Select
                    $hasError={!!fieldErrors.impresion_sobre_rayado}
                    value={formState.impresion_sobre_rayado}
                    onChange={(e) => handleInputChange('impresion_sobre_rayado', e.target.value)}
                  >
                    <option value="">Seleccione...</option>
                    <option value="SI">Sí</option>
                    <option value="NO">No</option>
                  </Select>
                </FormGroup>
                <FormGroup>
                  <Label>Total Clisse cm2</Label>
                  <Input
                    type="number"
                    min="0"
                    value={
                      // Issue 44: Cálculo automático de la suma de cm2_clisse_color_1 a 7
                      (formState.cm2_clisse_color_1 || 0) +
                      (formState.cm2_clisse_color_2 || 0) +
                      (formState.cm2_clisse_color_3 || 0) +
                      (formState.cm2_clisse_color_4 || 0) +
                      (formState.cm2_clisse_color_5 || 0) +
                      (formState.cm2_clisse_color_6 || 0) +
                      (formState.cm2_clisse_color_7 || 0)
                    }
                    readOnly
                    style={{ backgroundColor: '#e9ecef', cursor: 'not-allowed' }}
                    title="Calculado automáticamente como suma de Clisse cm2 1-7"
                  />
                </FormGroup>
              </div>
            </FormGridSection8>
          </SectionBody>
        </FormSection>

        {/* Secciones 9, 10 y 11 en fila horizontal como Laravel */}
        <SectionsRow>
          {/* Seccion 9: Medidas Interiores - Issue 45: readonly cuando se carga de CAD */}
          <FormSectionCompact>
            <SectionHeader>9.- Medidas Interiores {formState.cad_id ? '(desde CAD)' : ''}</SectionHeader>
            <SectionBody>
              <FormGrid $columns={1}>
                <FormGroup>
                  <Label>Largo (mm)</Label>
                  <Input
                    type="number"
                    value={formState.interno_largo || ''}
                    onChange={(e) => handleInputChange('interno_largo', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Ancho (mm)</Label>
                  <Input
                    type="number"
                    value={formState.interno_ancho || ''}
                    onChange={(e) => handleInputChange('interno_ancho', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Alto (mm)</Label>
                  <Input
                    type="number"
                    value={formState.interno_alto || ''}
                    onChange={(e) => handleInputChange('interno_alto', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
              </FormGrid>
            </SectionBody>
          </FormSectionCompact>

          {/* Seccion 10: Medidas Exteriores - Issue 46: readonly cuando se carga de CAD */}
          <FormSectionCompact>
            <SectionHeader>10.- Medidas Exteriores {formState.cad_id ? '(desde CAD)' : ''}</SectionHeader>
            <SectionBody>
              <FormGrid $columns={1}>
                <FormGroup>
                  <Label>Largo (mm)</Label>
                  <Input
                    type="number"
                    value={formState.externo_largo || ''}
                    onChange={(e) => handleInputChange('externo_largo', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Ancho (mm)</Label>
                  <Input
                    type="number"
                    value={formState.externo_ancho || ''}
                    onChange={(e) => handleInputChange('externo_ancho', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
                <FormGroup>
                  <Label>Alto (mm)</Label>
                  <Input
                    type="number"
                    value={formState.externo_alto || ''}
                    onChange={(e) => handleInputChange('externo_alto', e.target.value ? Number(e.target.value) : null)}
                    readOnly={!!formState.cad_id}
                    style={formState.cad_id ? { backgroundColor: '#e9ecef', cursor: 'not-allowed' } : undefined}
                    title={formState.cad_id ? 'Cargado desde CAD' : undefined}
                  />
                </FormGroup>
              </FormGrid>
            </SectionBody>
          </FormSectionCompact>

          {/* Seccion 11: Terminaciones */}
          <FormSectionCompact>
            <SectionHeader>11.- Terminaciones</SectionHeader>
            <SectionBody>
              <FormGrid $columns={1}>
                <FormGroup>
                  <Label>Proceso</Label>
                  <Select
                    value={formState.process_id || ''}
                    onChange={(e) => handleInputChange('process_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={optionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {filterOptions?.procesos.map(p => (
                      <option key={p.id} value={p.id}>{p.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.pegado_terminacion}>
                  <Label style={{ color: fieldErrors.pegado_terminacion ? '#dc3545' : undefined }}>Tipo Pegado</Label>
                  <Select
                    $hasError={!!fieldErrors.pegado_terminacion}
                    value={formState.pegado_terminacion || ''}
                    onChange={(e) => handleInputChange('pegado_terminacion', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.pegados?.map((opt) => (
                      <option key={opt.id} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.pegado_terminacion && <FieldError>{fieldErrors.pegado_terminacion}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.armado_id}>
                  <Label style={{ color: fieldErrors.armado_id ? '#dc3545' : undefined }}>Armado</Label>
                  <Select
                    $hasError={!!fieldErrors.armado_id}
                    value={formState.armado_id || ''}
                    onChange={(e) => handleInputChange('armado_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.armados?.map((opt) => (
                      <option key={opt.id} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.armado_id && <FieldError>{fieldErrors.armado_id}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.sentido_armado}>
                  <Label style={{ color: fieldErrors.sentido_armado ? '#dc3545' : undefined }}>Sentido de Armado *</Label>
                  <Select
                    $hasError={!!fieldErrors.sentido_armado}
                    value={formState.sentido_armado || ''}
                    onChange={(e) => handleInputChange('sentido_armado', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.sentidos_armado?.map((opt) => (
                      <option key={opt.id} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                  {fieldErrors.sentido_armado && <FieldError>{fieldErrors.sentido_armado}</FieldError>}
                </FormGroup>
                <FormGroup data-has-error={!!fieldErrors.maquila}>
                  <Label style={{ color: fieldErrors.maquila ? '#dc3545' : undefined }}>Maquila</Label>
                  <Select
                    $hasError={!!fieldErrors.maquila}
                    value={formState.maquila || ''}
                    onChange={(e) => handleInputChange('maquila', e.target.value ? Number(e.target.value) : null)}
                  >
                    <option value="">Seleccione...</option>
                    <option value={1}>Sí</option>
                    <option value={0}>No</option>
                  </Select>
                  {fieldErrors.maquila && <FieldError>{fieldErrors.maquila}</FieldError>}
                </FormGroup>
                <FormGroup>
                  <Label>Servicios Maquila</Label>
                  <Select
                    value={formState.maquila_servicio_id || ''}
                    onChange={(e) => handleInputChange('maquila_servicio_id', e.target.value ? Number(e.target.value) : null)}
                    disabled={formOptionsLoading}
                  >
                    <option value="">Seleccione...</option>
                    {formOptions?.maquila_servicios?.map((opt) => (
                      <option key={opt.id} value={opt.id}>{opt.nombre}</option>
                    ))}
                  </Select>
                </FormGroup>
              </FormGrid>
            </SectionBody>
          </FormSectionCompact>
        </SectionsRow>

        {/* Seccion 12: Secuencia Operacional */}
        <FormSection>
          <SectionHeader>12.- Secuencia Operacional</SectionHeader>
          <SectionBody>
            {/* Fila PLANTA */}
            <div style={{ marginBottom: '1rem', display: 'flex', alignItems: 'center', gap: '1rem' }}>
              <Label style={{ fontWeight: '600', minWidth: '60px' }}>PLANTA:</Label>
              <Select
                value={formState.so_planta_original || ''}
                onChange={(e) => handleInputChange('so_planta_original', e.target.value ? Number(e.target.value) : null)}
                style={{ width: '200px' }}
              >
                <option value="">Seleccionar...</option>
                {filterOptions?.plantas.map(p => (
                  <option key={p.id} value={p.id}>{p.nombre}</option>
                ))}
              </Select>
            </div>

            {/* Tabla 6x6 de secuencia operacional */}
            <div style={{ overflowX: 'auto' }}>
              <table style={{ width: '100%', borderCollapse: 'collapse', fontSize: '0.8rem' }}>
                <thead>
                  <tr style={{ backgroundColor: '#f3f4f6' }}>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ORIGINAL</th>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ALTERNATIVA 1</th>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ALTERNATIVA 2</th>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ALTERNATIVA 3</th>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ALTERNATIVA 4</th>
                    <th style={{ padding: '0.4rem', textAlign: 'center', fontWeight: '600', color: '#0891b2', minWidth: '130px' }}>ALTERNATIVA 5</th>
                  </tr>
                </thead>
                <tbody>
                  {[0, 1, 2, 3, 4, 5].map((rowIndex) => (
                    <tr key={rowIndex}>
                      {[0, 1, 2, 3, 4, 5].map((colIndex) => (
                        <td key={colIndex} style={{ padding: '0.3rem', borderBottom: '1px solid #e5e7eb' }}>
                          <Select
                            value={formState.secuencia_operacional_matrix[rowIndex]?.[colIndex] || ''}
                            onChange={(e) => {
                              const newMatrix = formState.secuencia_operacional_matrix.map((row, ri) =>
                                ri === rowIndex
                                  ? row.map((cell, ci) => ci === colIndex ? (e.target.value || null) : cell)
                                  : [...row]
                              );
                              handleInputChange('secuencia_operacional_matrix', newMatrix);
                            }}
                            style={{ width: '100%', fontSize: '0.8rem', padding: '0.25rem' }}
                          >
                            <option value="">Seleccionar...</option>
                            {formOptions?.secuencia_operacional
                              ?.filter(s => !formState.so_planta_original || s.planta_id === formState.so_planta_original)
                              .map(s => (
                              <option key={s.id} value={s.id}>{s.descripcion || s.nombre}</option>
                            ))}
                          </Select>
                        </td>
                      ))}
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
          </SectionBody>
        </FormSection>

        {/* Seccion 13: Material Asignado - Issue 51-52: Campos bloqueados excepto Super Admin */}
        <FormSection>
          <SectionHeader>13.- Material Asignado</SectionHeader>
          <SectionBody>
            <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem', maxWidth: '100%' }}>
              <FormGroup>
                <Label>MATERIAL ASIGNADO</Label>
                <Input
                  type="text"
                  value={formState.material_asignado}
                  onChange={(e) => handleInputChange('material_asignado', e.target.value)}
                  placeholder="Código de material..."
                  disabled={getCurrentUserRole() !== ROLES.SuperAdministrador}
                  style={getCurrentUserRole() !== ROLES.SuperAdministrador ? { backgroundColor: '#f5f5f5' } : undefined}
                />
              </FormGroup>

              <FormGroup>
                <Label>DESCRIPCIÓN</Label>
                <Input
                  type="text"
                  value={formState.descripcion_material}
                  onChange={(e) => handleInputChange('descripcion_material', e.target.value)}
                  placeholder="Descripción del material..."
                  disabled={getCurrentUserRole() !== ROLES.SuperAdministrador}
                  style={getCurrentUserRole() !== ROLES.SuperAdministrador ? { backgroundColor: '#f5f5f5' } : undefined}
                />
              </FormGroup>
            </div>
          </SectionBody>
        </FormSection>

        {/* Secciones 13 y 14 lado a lado como en Laravel */}
        <div style={{ display: 'grid', gridTemplateColumns: '65% 35%', gap: '0.5rem', maxWidth: '100%', overflow: 'hidden' }}>
          {/* Seccion 13: Datos para Desarrollo */}
          <FormSection style={{ margin: 0, minWidth: 0, overflow: 'hidden', maxWidth: '100%' }}>
            <SectionHeader>13.- Datos para desarrollo</SectionHeader>
            <SectionBody style={{ overflow: 'hidden', padding: '0.5rem' }}>
              {/* Campos en columna única con label a la izquierda */}
              <div style={{ display: 'flex', flexDirection: 'column', gap: '0.25rem', fontSize: '0.7rem', maxWidth: '100%' }}>
                {/* Fila 1 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.product_type_developing_id ? '#dc3545' : undefined }}>TIPO PRODUCTO:</Label>
                  <Select
                    $hasError={!!fieldErrors.product_type_developing_id}
                    value={formState.product_type_developing_id || ''}
                    onChange={(e) => handleInputChange('product_type_developing_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.product_type_developing?.map(pt => (
                      <option key={pt.id} value={pt.id}>{pt.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.peso_contenido_caja ? '#dc3545' : undefined }}>PESO CAJA (KG):</Label>
                  <Input
                    $hasError={!!fieldErrors.peso_contenido_caja}
                    type="number"
                    value={formState.peso_contenido_caja || ''}
                    onChange={(e) => handleInputChange('peso_contenido_caja', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  />
                </div>

                {/* Fila 2 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.food_type_id ? '#dc3545' : undefined }}>TIPO ALIMENTO:</Label>
                  <Select
                    $hasError={!!fieldErrors.food_type_id}
                    value={formState.food_type_id || ''}
                    onChange={(e) => handleInputChange('food_type_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: (formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)) ? '#f5f5f5' : undefined }}
                    disabled={formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.food_types?.map(ft => (
                      <option key={ft.id} value={ft.id}>{ft.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.autosoportante ? '#dc3545' : undefined }}>AUTOSOPORTANTE:</Label>
                  <Select
                    $hasError={!!fieldErrors.autosoportante}
                    value={formState.autosoportante === null ? '' : (formState.autosoportante ? '1' : '0')}
                    onChange={(e) => handleInputChange('autosoportante', e.target.value === '' ? null : e.target.value === '1')}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="0">No</option>
                    <option value="1">Si</option>
                  </Select>
                </div>

                {/* Fila 3 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.expected_use_id ? '#dc3545' : undefined }}>USO PREVISTO:</Label>
                  <Select
                    $hasError={!!fieldErrors.expected_use_id}
                    value={formState.expected_use_id || ''}
                    onChange={(e) => handleInputChange('expected_use_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: (formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)) ? '#f5f5f5' : undefined }}
                    disabled={formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.expected_uses?.map(eu => (
                      <option key={eu.id} value={eu.id}>{eu.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.envase_id ? '#dc3545' : undefined }}>ENVASE PRIMARIO:</Label>
                  <Select
                    $hasError={!!fieldErrors.envase_id}
                    value={formState.envase_id || ''}
                    onChange={(e) => handleInputChange('envase_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.envases?.map(env => (
                      <option key={env.id} value={env.id}>{env.nombre}</option>
                    ))}
                  </Select>
                </div>

                {/* Fila 4 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.recycled_use_id ? '#dc3545' : undefined }}>USO RECICLADO:</Label>
                  <Select
                    $hasError={!!fieldErrors.recycled_use_id}
                    value={formState.recycled_use_id || ''}
                    onChange={(e) => handleInputChange('recycled_use_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: (formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)) ? '#f5f5f5' : undefined }}
                    disabled={formState.product_type_developing_id === 1 || (formState.product_type_developing_id !== 3 && formState.product_type_developing_id !== null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.recycled_uses?.map(ru => (
                      <option key={ru.id} value={ru.id}>{ru.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.cantidad_cajas_apiladas ? '#dc3545' : undefined }}>CAJAS APILADAS:</Label>
                  <Input
                    $hasError={!!fieldErrors.cantidad_cajas_apiladas}
                    type="number"
                    value={formState.cantidad_cajas_apiladas || ''}
                    onChange={(e) => handleInputChange('cantidad_cajas_apiladas', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  />
                </div>

                {/* Fila 5 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.class_substance_packed_id ? '#dc3545' : undefined }}>CLASE SUSTANCIA:</Label>
                  <Select
                    $hasError={!!fieldErrors.class_substance_packed_id}
                    value={formState.class_substance_packed_id || ''}
                    onChange={(e) => handleInputChange('class_substance_packed_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: (formState.product_type_developing_id === 3 || (formState.product_type_developing_id !== 1 && formState.product_type_developing_id !== null)) ? '#f5f5f5' : undefined }}
                    disabled={formState.product_type_developing_id === 3 || (formState.product_type_developing_id !== 1 && formState.product_type_developing_id !== null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.class_substance_packeds?.map(cs => (
                      <option key={cs.id} value={cs.id}>{cs.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.pallet_sobre_pallet ? '#dc3545' : undefined }}>PALLET S/ PALLET:</Label>
                  <Select
                    $hasError={!!fieldErrors.pallet_sobre_pallet}
                    value={formState.pallet_sobre_pallet === null ? '' : (formState.pallet_sobre_pallet ? '1' : '0')}
                    onChange={(e) => handleInputChange('pallet_sobre_pallet', e.target.value === '' ? null : e.target.value === '1')}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  >
                    <option value="">Seleccionar...</option>
                    <option value="0">No</option>
                    <option value="1">Si</option>
                  </Select>
                </div>

                {/* Fila 6 */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.transportation_way_id ? '#dc3545' : undefined }}>MEDIO TRANSPORTE:</Label>
                  <Select
                    $hasError={!!fieldErrors.transportation_way_id}
                    value={formState.transportation_way_id || ''}
                    onChange={(e) => handleInputChange('transportation_way_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: (formState.product_type_developing_id === 3 || (formState.product_type_developing_id !== 1 && formState.product_type_developing_id !== null)) ? '#f5f5f5' : undefined }}
                    disabled={formState.product_type_developing_id === 3 || (formState.product_type_developing_id !== 1 && formState.product_type_developing_id !== null)}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.transportation_ways?.map(tw => (
                      <option key={tw.id} value={tw.id}>{tw.nombre}</option>
                    ))}
                  </Select>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.cantidad ? '#dc3545' : undefined }}>CANTIDAD:</Label>
                  <Input
                    $hasError={!!fieldErrors.cantidad}
                    type="number"
                    value={formState.cantidad || ''}
                    onChange={(e) => handleInputChange('cantidad', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem', backgroundColor: formState.pallet_sobre_pallet !== true ? '#f5f5f5' : undefined }}
                    disabled={formState.pallet_sobre_pallet !== true}
                  />
                </div>

                {/* Fila 7 - Mercado Destino */}
                <div style={{ display: 'grid', gridTemplateColumns: '140px 1fr 140px 1fr', gap: '0.25rem', alignItems: 'center' }}>
                  <Label style={{ margin: 0, fontWeight: '600', fontSize: '0.65rem', whiteSpace: 'nowrap', color: fieldErrors.target_market_id ? '#dc3545' : undefined }}>MERCADO DESTINO:</Label>
                  <Select
                    $hasError={!!fieldErrors.target_market_id}
                    value={formState.target_market_id || ''}
                    onChange={(e) => handleInputChange('target_market_id', e.target.value ? Number(e.target.value) : null)}
                    style={{ width: '100%', fontSize: '0.7rem', padding: '0.15rem' }}
                  >
                    <option value="">Seleccionar...</option>
                    {formOptions?.target_markets?.map(tm => (
                      <option key={tm.id} value={tm.id}>{tm.nombre}</option>
                    ))}
                  </Select>
                  <div></div>
                  <div></div>
                </div>
              </div>
            </SectionBody>
          </FormSection>

          {/* Seccion 14: Observacion del trabajo */}
          <FormSection style={{ margin: 0, minWidth: 0, overflow: 'hidden' }} data-has-error={!!fieldErrors.observacion}>
            <SectionHeader style={{ background: fieldErrors.observacion ? 'linear-gradient(135deg, #dc3545 0%, #c82333 100%)' : undefined }}>
              14.- Observación del trabajo a realizar *
            </SectionHeader>
            <SectionBody style={{ padding: '0.5rem', overflow: 'hidden' }}>
              <TextArea
                $hasError={!!fieldErrors.observacion}
                value={formState.observacion}
                onChange={(e) => handleInputChange('observacion', e.target.value)}
                placeholder="Observaciones adicionales (mínimo 10 caracteres)..."
                style={{ width: '100%', height: '200px', resize: 'none', fontSize: '0.75rem', boxSizing: 'border-box' }}
              />
              {fieldErrors.observacion && <FieldError>{fieldErrors.observacion}</FieldError>}
            </SectionBody>
          </FormSection>
        </div>

        {/* Botones de accion */}
        <ButtonGroup>
          <BackButton type="button" onClick={handleReset}>
            Limpiar Formulario
          </BackButton>
          <SubmitButton
            type="submit"
            disabled={createMutation.isPending}
          >
            {createMutation.isPending ? 'Creando...' : 'Crear Orden de Trabajo'}
          </SubmitButton>
        </ButtonGroup>
          </>
        )}
      </form>

      {/* Modal de Crear Muestra */}
      <MuestraModal
        isOpen={showMuestraModal}
        onClose={() => {
          setShowMuestraModal(false);
          // Si se cierra el modal sin guardar, desmarcar el checkbox
          if (!formState.numero_muestras) {
            handleInputChange('muestra', false);
          }
        }}
        onSave={(_muestraData, numeroMuestras) => {
          // Guardar los datos de la muestra y cerrar el modal
          handleInputChange('muestra', true);
          handleInputChange('numero_muestras', numeroMuestras);
          setShowMuestraModal(false);
        }}
        cadId={formState.cascadeData.cartonId}
        cartonId={formState.cascadeData.cartonId}
        tipoSolicitud={formState.tipo_solicitud}
        cads={formOptions?.cads || []}
        cartones={formOptions?.cartons || []}
        cartonesMuestra={formOptions?.cartons || []}
        comunas={formOptions?.comunas || []}
        roleId={getCurrentUserRole()}
      />
    </Container>
  );
}
