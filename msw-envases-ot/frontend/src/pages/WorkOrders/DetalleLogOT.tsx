/**
 * DetalleLogOT Component
 * Historial detallado de cambios (log/bitacora) de una OT especifica
 * Muestra cada modificacion con campo, valor antiguo, valor nuevo y usuario
 */

import { useState, useCallback, useMemo } from 'react';
import styled from 'styled-components';
import { theme } from '../../theme';
import { useQuery } from '@tanstack/react-query';

// Styled Components
const Container = styled.div`
  padding: 1.5rem;
  max-width: 100%;
`;

const BackLink = styled.button`
  background: none;
  border: none;
  color: ${theme.colors.primary};
  font-size: 1rem;
  cursor: pointer;
  padding: 0;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
  gap: 0.25rem;

  &:hover {
    text-decoration: underline;
  }
`;

const Title = styled.h1`
  font-size: 1.5rem;
  font-weight: 600;
  color: ${theme.colors.textPrimary};
  margin: 0 0 1.5rem 0;
`;

const FiltersCard = styled.div`
  background: white;
  border: 1px solid ${theme.colors.border};
  border-radius: 8px;
  padding: 1rem;
  margin-bottom: 1.5rem;
`;

const FiltersGrid = styled.div`
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 0.75rem;
  align-items: end;
`;

const FormGroup = styled.div`
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
`;

const Label = styled.label`
  font-size: 0.7rem;
  font-weight: 500;
  color: ${theme.colors.textSecondary};
  text-transform: uppercase;
`;

const Input = styled.input`
  padding: 0.4rem 0.5rem;
  border: 1px solid ${theme.colors.border};
  border-radius: 4px;
  font-size: 0.8rem;

  &:focus {
    outline: none;
    border-color: ${theme.colors.primary};
    box-shadow: 0 0 0 2px ${theme.colors.primary}20;
  }
`;

const Select = styled.select`
  padding: 0.4rem 0.5rem;
  border: 1px solid ${theme.colors.border};
  border-radius: 4px;
  font-size: 0.8rem;
  background: white;

  &:focus {
    outline: none;
    border-color: ${theme.colors.primary};
    box-shadow: 0 0 0 2px ${theme.colors.primary}20;
  }
`;

const FiltersActions = styled.div`
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  margin-top: 1rem;
`;

const Button = styled.button<{ $variant?: 'primary' | 'secondary' | 'outline' }>`
  padding: 0.4rem 1rem;
  border: none;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s;

  ${({ $variant }) => {
    switch ($variant) {
      case 'primary':
        return `
          background: ${theme.colors.primary};
          color: white;
          &:hover { opacity: 0.9; }
        `;
      case 'outline':
        return `
          background: transparent;
          border: 1px solid ${theme.colors.primary};
          color: ${theme.colors.primary};
          &:hover { background: ${theme.colors.primary}10; }
        `;
      default:
        return `
          background: ${theme.colors.bgLight};
          color: ${theme.colors.textSecondary};
          border: 1px solid ${theme.colors.border};
          &:hover { background: white; }
        `;
    }
  }}
`;

const TableContainer = styled.div`
  background: white;
  border: 1px solid ${theme.colors.border};
  border-radius: 8px;
  overflow: hidden;
`;

const Table = styled.table`
  width: 100%;
  border-collapse: collapse;
`;

const Th = styled.th`
  padding: 0.75rem;
  text-align: left;
  font-size: 0.75rem;
  font-weight: 600;
  color: ${theme.colors.textSecondary};
  text-transform: uppercase;
  background: ${theme.colors.bgLight};
  border-bottom: 1px solid ${theme.colors.border};
`;

const Td = styled.td`
  padding: 0.75rem;
  font-size: 0.8rem;
  color: ${theme.colors.textPrimary};
  border-bottom: 1px solid ${theme.colors.border};
  vertical-align: top;
`;

const ValueCell = styled.span<{ $type: 'old' | 'new' }>`
  display: inline-block;
  padding: 0.2rem 0.4rem;
  border-radius: 4px;
  font-size: 0.75rem;
  background: ${({ $type }) => $type === 'old' ? `${theme.colors.danger}10` : `${theme.colors.success}10`};
  color: ${({ $type }) => $type === 'old' ? theme.colors.danger : theme.colors.success};
`;

const EmptyValue = styled.span`
  color: ${theme.colors.textSecondary};
  font-style: italic;
  font-size: 0.75rem;
`;

const LoadingOverlay = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 3rem;
  color: ${theme.colors.textSecondary};
`;

const Spinner = styled.div`
  width: 24px;
  height: 24px;
  border: 3px solid ${theme.colors.border};
  border-top-color: ${theme.colors.primary};
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-right: 0.75rem;

  @keyframes spin {
    to { transform: rotate(360deg); }
  }
`;

const EmptyState = styled.div`
  text-align: center;
  padding: 3rem;
  color: ${theme.colors.textSecondary};
`;

const Pagination = styled.div`
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  padding: 1rem;
  border-top: 1px solid ${theme.colors.border};
`;

const PageButton = styled.button<{ $active?: boolean }>`
  padding: 0.4rem 0.75rem;
  border: 1px solid ${({ $active }) => $active ? theme.colors.primary : theme.colors.border};
  background: ${({ $active }) => $active ? theme.colors.primary : 'white'};
  color: ${({ $active }) => $active ? 'white' : theme.colors.textPrimary};
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.8rem;

  &:hover:not(:disabled) {
    border-color: ${theme.colors.primary};
  }

  &:disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
`;

const OperationBadge = styled.span<{ $type: 'Modificación' | 'Insercion' | 'Eliminacion' }>`
  display: inline-block;
  padding: 0.2rem 0.5rem;
  border-radius: 4px;
  font-size: 0.7rem;
  font-weight: 500;

  ${({ $type }) => {
    switch ($type) {
      case 'Modificación':
        return `background: ${theme.colors.warning}20; color: ${theme.colors.warning};`;
      case 'Insercion':
        return `background: ${theme.colors.success}20; color: ${theme.colors.success};`;
      case 'Eliminacion':
        return `background: ${theme.colors.danger}20; color: ${theme.colors.danger};`;
      default:
        return `background: ${theme.colors.bgLight}; color: ${theme.colors.textSecondary};`;
    }
  }}
`;

// Types
interface LogEntry {
  id: number;
  work_order_id: number;
  created_at: string;
  observacion: string;
  operacion: 'Modificación' | 'Insercion' | 'Eliminacion';
  user_data: {
    nombre: string;
    apellido: string;
  };
  datos_modificados: {
    texto: string;
    antiguo_valor?: { descripcion: string };
    nuevo_valor?: { descripcion: string };
    valor?: { descripcion: string };
  }[];
}

interface DetalleLogOTProps {
  otId: number;
  onNavigate: (page: string, id?: number) => void;
}

// Mock data for demonstration
const mockLogEntries: LogEntry[] = [
  {
    id: 1,
    work_order_id: 1234,
    created_at: '2025-12-20 10:30:00',
    observacion: 'Actualizacion de datos comerciales',
    operacion: 'Modificación',
    user_data: { nombre: 'Juan', apellido: 'Perez' },
    datos_modificados: [
      { texto: 'Cliente', antiguo_valor: { descripcion: 'Cliente Antiguo' }, nuevo_valor: { descripcion: 'Cliente Nuevo' } },
      { texto: 'Descripcion', antiguo_valor: { descripcion: 'Desc 1' }, nuevo_valor: { descripcion: 'Desc Actualizada' } },
    ],
  },
  {
    id: 2,
    work_order_id: 1234,
    created_at: '2025-12-19 15:45:00',
    observacion: 'Cambio de estado',
    operacion: 'Modificación',
    user_data: { nombre: 'Maria', apellido: 'Garcia' },
    datos_modificados: [
      { texto: 'Estado', antiguo_valor: { descripcion: 'En Proceso' }, nuevo_valor: { descripcion: 'Completado' } },
    ],
  },
  {
    id: 3,
    work_order_id: 1234,
    created_at: '2025-12-18 09:00:00',
    observacion: 'Creacion de OT',
    operacion: 'Insercion',
    user_data: { nombre: 'Pedro', apellido: 'Lopez' },
    datos_modificados: [
      { texto: 'OT Creada', valor: { descripcion: 'Nueva OT #1234' } },
    ],
  },
];

export default function DetalleLogOT({ otId, onNavigate }: DetalleLogOTProps) {
  const [dateDesde, setDateDesde] = useState('');
  const [dateHasta, setDateHasta] = useState('');
  const [cambioId, setCambioId] = useState('');
  const [campoId, setCampoId] = useState('');
  const [userId, setUserId] = useState('');
  const [currentPage, setCurrentPage] = useState(1);

  // TODO: Replace with real API call
  const { data: logEntries, isLoading } = useQuery({
    queryKey: ['ot-log', otId, dateDesde, dateHasta, cambioId, campoId, userId, currentPage],
    queryFn: async () => {
      // Simulated API call
      await new Promise(resolve => setTimeout(resolve, 500));
      return mockLogEntries;
    },
  });

  const handleSearch = useCallback((e: React.FormEvent) => {
    e.preventDefault();
    setCurrentPage(1);
    // Query will refetch automatically due to queryKey change
  }, []);

  const handleClearFilters = useCallback(() => {
    setDateDesde('');
    setDateHasta('');
    setCambioId('');
    setCampoId('');
    setUserId('');
    setCurrentPage(1);
  }, []);

  const handleExport = useCallback(() => {
    // TODO: Implement Excel export
    console.log('Exporting log for OT:', otId);
    alert('Exportacion en desarrollo');
  }, [otId]);

  // Flatten log entries for table display
  const flattenedEntries = useMemo(() => {
    if (!logEntries) return [];

    return logEntries.flatMap(entry =>
      entry.datos_modificados.map((mod, idx) => ({
        ...entry,
        modIndex: idx,
        campo: mod.texto,
        valorAntiguo: entry.operacion === 'Modificación'
          ? mod.antiguo_valor?.descripcion || ''
          : 'N/A',
        valorNuevo: entry.operacion === 'Modificación'
          ? mod.nuevo_valor?.descripcion || ''
          : mod.valor?.descripcion || '',
      }))
    );
  }, [logEntries]);

  const formatDateTime = (dateStr: string) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleString('es-CL', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    });
  };

  return (
    <Container>
      <BackLink onClick={() => onNavigate('gestionar-ot', otId)}>← Volver a OT #{otId}</BackLink>
      <Title>Historial de Cambios - OT #{otId}</Title>

      {/* Filters */}
      <FiltersCard>
        <form onSubmit={handleSearch}>
          <FiltersGrid>
            <FormGroup>
              <Label>Desde</Label>
              <Input
                type="date"
                value={dateDesde}
                onChange={(e) => setDateDesde(e.target.value)}
              />
            </FormGroup>
            <FormGroup>
              <Label>Hasta</Label>
              <Input
                type="date"
                value={dateHasta}
                onChange={(e) => setDateHasta(e.target.value)}
              />
            </FormGroup>
            <FormGroup>
              <Label>ID Cambio</Label>
              <Input
                type="number"
                placeholder="ID..."
                value={cambioId}
                onChange={(e) => setCambioId(e.target.value)}
              />
            </FormGroup>
            <FormGroup>
              <Label>Campo Modificado</Label>
              <Select value={campoId} onChange={(e) => setCampoId(e.target.value)}>
                <option value="">Todos</option>
                <option value="cliente">Cliente</option>
                <option value="descripcion">Descripcion</option>
                <option value="estado">Estado</option>
                <option value="area">Area</option>
              </Select>
            </FormGroup>
            <FormGroup>
              <Label>Usuario</Label>
              <Select value={userId} onChange={(e) => setUserId(e.target.value)}>
                <option value="">Todos</option>
                {/* TODO: Load users from API */}
              </Select>
            </FormGroup>
          </FiltersGrid>
          <FiltersActions>
            <Button type="submit" $variant="primary">Filtrar</Button>
            <Button type="button" onClick={handleClearFilters}>Limpiar</Button>
            <Button type="button" $variant="outline" onClick={handleExport}>Exportar</Button>
          </FiltersActions>
        </form>
      </FiltersCard>

      {/* Results Table */}
      <TableContainer>
        {isLoading ? (
          <LoadingOverlay>
            <Spinner />
            <span>Cargando historial...</span>
          </LoadingOverlay>
        ) : flattenedEntries.length === 0 ? (
          <EmptyState>
            <p>No hay registros de cambios para esta OT</p>
          </EmptyState>
        ) : (
          <>
            <Table>
              <thead>
                <tr>
                  <Th style={{ width: '8%' }}>OT</Th>
                  <Th style={{ width: '8%' }}>ID Cambio</Th>
                  <Th style={{ width: '12%' }}>Fecha</Th>
                  <Th style={{ width: '15%' }}>Descripcion</Th>
                  <Th style={{ width: '12%' }}>Campo</Th>
                  <Th style={{ width: '15%' }}>Valor Antiguo</Th>
                  <Th style={{ width: '15%' }}>Valor Nuevo</Th>
                  <Th style={{ width: '15%' }}>Usuario</Th>
                </tr>
              </thead>
              <tbody>
                {flattenedEntries.map((entry) => (
                  <tr key={`${entry.id}-${entry.modIndex}`}>
                    <Td>{entry.work_order_id}</Td>
                    <Td>{entry.id}</Td>
                    <Td>{formatDateTime(entry.created_at)}</Td>
                    <Td>
                      <OperationBadge $type={entry.operacion}>{entry.operacion}</OperationBadge>
                      <div style={{ marginTop: '0.25rem', fontSize: '0.75rem' }}>{entry.observacion}</div>
                    </Td>
                    <Td>{entry.campo}</Td>
                    <Td>
                      {entry.valorAntiguo === 'N/A' ? (
                        <EmptyValue>N/A</EmptyValue>
                      ) : entry.valorAntiguo ? (
                        <ValueCell $type="old">{entry.valorAntiguo}</ValueCell>
                      ) : (
                        <EmptyValue>Campo Vacio</EmptyValue>
                      )}
                    </Td>
                    <Td>
                      {entry.valorNuevo ? (
                        <ValueCell $type="new">{entry.valorNuevo}</ValueCell>
                      ) : (
                        <EmptyValue>Campo Vacio</EmptyValue>
                      )}
                    </Td>
                    <Td>{entry.user_data.nombre} {entry.user_data.apellido}</Td>
                  </tr>
                ))}
              </tbody>
            </Table>

            {/* Pagination */}
            <Pagination>
              <PageButton
                disabled={currentPage === 1}
                onClick={() => setCurrentPage(p => p - 1)}
              >
                Anterior
              </PageButton>
              <PageButton $active>{currentPage}</PageButton>
              <PageButton onClick={() => setCurrentPage(p => p + 1)}>
                Siguiente
              </PageButton>
            </Pagination>
          </>
        )}
      </TableContainer>
    </Container>
  );
}
