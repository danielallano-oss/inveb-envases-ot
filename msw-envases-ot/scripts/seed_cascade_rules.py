"""
Seeder de reglas de cascada.
FASE 3: Equivalente Python del CascadeRulesTableSeeder de Laravel.

Uso: python scripts/seed_cascade_rules.py
"""
import sys
import os

# Agregar src al path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', 'src'))

from sqlmodel import Session
from app.db import engine
from app.models import CascadeRule

# Datos de reglas de cascada
CASCADE_RULES = [
    # CASC-001: TIPO ITEM -> IMPRESION
    {
        "rule_code": "CASC-001",
        "rule_name": "Tipo Item habilita Impresion",
        "trigger_field": "product_type_id",
        "trigger_table": "product_types",
        "target_field": "impresion",
        "target_table": "impresion",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["impresion", "fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/product-type",
        "cascade_order": 1,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar tipo de item, se habilita impresion y se resetean todos los campos siguientes",
    },
    # CASC-002: IMPRESION -> FSC
    {
        "rule_code": "CASC-002",
        "rule_name": "Impresion habilita FSC",
        "trigger_field": "impresion",
        "trigger_table": "impresion",
        "target_field": "fsc",
        "target_table": "fsc",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["fsc", "cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/impresion",
        "cascade_order": 2,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar impresion, se habilita FSC. Valida combinacion con tabla de reglas",
    },
    # CASC-003: FSC -> CINTA
    {
        "rule_code": "CASC-003",
        "rule_name": "FSC habilita Cinta",
        "trigger_field": "fsc",
        "trigger_table": "fsc",
        "target_field": "cinta",
        "target_table": "tipos_cintas",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["cinta", "coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/fsc",
        "cascade_order": 3,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar FSC, se habilita Cinta",
    },
    # CASC-004: CINTA -> RECUBRIMIENTO INTERNO
    {
        "rule_code": "CASC-004",
        "rule_name": "Cinta habilita Recubrimiento Interno",
        "trigger_field": "cinta",
        "trigger_table": "tipos_cintas",
        "target_field": "coverage_internal_id",
        "target_table": "coverage_internals",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["coverage_internal_id", "coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/cinta",
        "cascade_order": 4,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar Cinta, se habilita Recubrimiento Interno",
    },
    # CASC-005: REC. INTERNO -> REC. EXTERNO
    {
        "rule_code": "CASC-005",
        "rule_name": "Rec. Interno habilita Rec. Externo",
        "trigger_field": "coverage_internal_id",
        "trigger_table": "coverage_internals",
        "target_field": "coverage_external_id",
        "target_table": "coverage_externals",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["coverage_external_id", "planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/coverage-internal",
        "cascade_order": 5,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar Recubrimiento Interno, se habilita Recubrimiento Externo",
    },
    # CASC-006: REC. EXTERNO -> PLANTA
    {
        "rule_code": "CASC-006",
        "rule_name": "Rec. Externo habilita Planta",
        "trigger_field": "coverage_external_id",
        "trigger_table": "coverage_externals",
        "target_field": "planta_id",
        "target_table": "plantas",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["planta_id", "carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/coverage-external",
        "cascade_order": 6,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar Recubrimiento Externo, se habilita Planta",
    },
    # CASC-007: PLANTA -> COLOR CARTON
    {
        "rule_code": "CASC-007",
        "rule_name": "Planta habilita Color Carton",
        "trigger_field": "planta_id",
        "trigger_table": "plantas",
        "target_field": "carton_color",
        "target_table": None,
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["carton_color", "carton_id"]',
        "validation_endpoint": "/api/cascade/planta",
        "cascade_order": 7,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar Planta, se habilita Color Carton",
    },
    # CASC-008: COLOR CARTON -> CARTON
    {
        "rule_code": "CASC-008",
        "rule_name": "Color Carton habilita Carton",
        "trigger_field": "carton_color",
        "trigger_table": None,
        "target_field": "carton_id",
        "target_table": "cartons",
        "action": "enable",
        "condition_type": "hasValue",
        "condition_value": None,
        "reset_fields": '["carton_id"]',
        "validation_endpoint": "/api/cascade/carton-color",
        "cascade_order": 8,
        "form_context": "ot",
        "active": True,
        "description": "Al seleccionar Color Carton, se habilita Carton",
    },
    # EXCEP-001: MuestraConCad salta recubrimientos
    {
        "rule_code": "EXCEP-001",
        "rule_name": "MuestraConCad salta recubrimientos",
        "trigger_field": "tipo_solicitud",
        "trigger_table": None,
        "target_field": "coverage_internal_id",
        "target_table": "coverage_internals",
        "action": "setValue",
        "condition_type": "equals",
        "condition_value": '{"tipo_solicitud": 3, "role": [4, 19]}',
        "reset_fields": None,
        "validation_endpoint": None,
        "cascade_order": 100,
        "form_context": "ot",
        "active": True,
        "description": "Cuando tipo_solicitud=3 (MuestraConCad) y usuario es Vendedor, coverage_internal=14 (N/A)",
    },
    # EXCEP-002: MuestraConCad salta rec externo
    {
        "rule_code": "EXCEP-002",
        "rule_name": "MuestraConCad salta rec externo",
        "trigger_field": "tipo_solicitud",
        "trigger_table": None,
        "target_field": "coverage_external_id",
        "target_table": "coverage_externals",
        "action": "setValue",
        "condition_type": "equals",
        "condition_value": '{"tipo_solicitud": 3, "role": [4, 19]}',
        "reset_fields": None,
        "validation_endpoint": None,
        "cascade_order": 101,
        "form_context": "ot",
        "active": True,
        "description": "Cuando tipo_solicitud=3 (MuestraConCad) y usuario es Vendedor, coverage_external=14 (N/A)",
    },
]


def seed_cascade_rules():
    """Insertar reglas de cascada en la base de datos."""
    print("Seeding cascade rules...")

    with Session(engine) as session:
        # Limpiar tabla existente
        from sqlmodel import delete
        session.exec(delete(CascadeRule))
        session.commit()

        # Insertar reglas
        for rule_data in CASCADE_RULES:
            rule = CascadeRule(**rule_data)
            session.add(rule)

        session.commit()
        print(f"Inserted {len(CASCADE_RULES)} cascade rules.")


if __name__ == "__main__":
    seed_cascade_rules()
