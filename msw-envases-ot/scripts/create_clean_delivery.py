"""
Script para crear versión limpia de entrega sin archivos de prueba
"""
import os
import shutil

# Rutas
SOURCE = r"c:\Users\pg_54\OneDrive\Documentos\Proyectos\Tecnoandina\inveb\invebchile-envases-ot-00e7b5a341a2\invebchile-envases-ot-00e7b5a341a2\msw-envases-ot"
DEST = r"C:\Users\pg_54\Downloads\msw-envases-ot-v3"

# Carpetas a excluir completamente
EXCLUDE_DIRS = {
    'node_modules',
    '__pycache__',
    '.git',
    '.venv',
    'venv',
    '.idea',
    '.vscode',
    'scripts',  # Excluir carpeta scripts completa
    'files',    # Excluir carpeta files de prueba
    'screenshots',
}

# Archivos a excluir
EXCLUDE_FILES = {
    '.env',
    '.DS_Store',
    'Thumbs.db',
    'test_upload.txt',
}

# Extensiones a excluir
EXCLUDE_EXTENSIONS = {
    '.pyc',
    '.pyo',
    '.log',
}

# Nombres reservados de Windows
RESERVED_NAMES = {'con', 'prn', 'aux', 'nul', 'com1', 'com2', 'com3', 'com4',
                  'com5', 'com6', 'com7', 'com8', 'com9', 'lpt1', 'lpt2',
                  'lpt3', 'lpt4', 'lpt5', 'lpt6', 'lpt7', 'lpt8', 'lpt9'}

def should_skip(path, name):
    """Determina si un archivo/carpeta debe omitirse"""
    name_lower = name.lower()

    # Nombres reservados de Windows
    base_name = os.path.splitext(name_lower)[0]
    if base_name in RESERVED_NAMES:
        return True

    # Carpetas excluidas
    if os.path.isdir(path) and name_lower in EXCLUDE_DIRS:
        return True

    # Archivos excluidos
    if name_lower in EXCLUDE_FILES:
        return True

    # Extensiones excluidas
    _, ext = os.path.splitext(name_lower)
    if ext in EXCLUDE_EXTENSIONS:
        return True

    return False

def copy_tree(src, dst):
    """Copia recursiva con filtros"""
    copied_files = 0
    skipped = 0

    for item in os.listdir(src):
        src_path = os.path.join(src, item)
        dst_path = os.path.join(dst, item)

        if should_skip(src_path, item):
            skipped += 1
            continue

        try:
            if os.path.isdir(src_path):
                os.makedirs(dst_path, exist_ok=True)
                sub_copied, sub_skipped = copy_tree(src_path, dst_path)
                copied_files += sub_copied
                skipped += sub_skipped
            else:
                shutil.copy2(src_path, dst_path)
                copied_files += 1
        except Exception as e:
            print(f"  [SKIP] {item}: {e}")
            skipped += 1

    return copied_files, skipped

def main():
    print("=" * 60)
    print("CREANDO VERSION LIMPIA DE ENTREGA v3")
    print("=" * 60)

    # Eliminar destino si existe
    if os.path.exists(DEST):
        print(f"\nEliminando version anterior: {DEST}")
        shutil.rmtree(DEST)

    # Crear destino
    os.makedirs(DEST)

    print(f"\nCopiando desde: {SOURCE}")
    print(f"Hacia: {DEST}")
    print("\nExcluyendo:")
    print(f"  - Carpetas: {EXCLUDE_DIRS}")
    print(f"  - Archivos: {EXCLUDE_FILES}")
    print(f"  - Extensiones: {EXCLUDE_EXTENSIONS}")

    # Copiar
    copied, skipped = copy_tree(SOURCE, DEST)

    # Calcular tamaño
    total_size = 0
    for root, dirs, files in os.walk(DEST):
        for f in files:
            total_size += os.path.getsize(os.path.join(root, f))

    print("\n" + "=" * 60)
    print("RESULTADO:")
    print(f"  Archivos copiados: {copied}")
    print(f"  Archivos omitidos: {skipped}")
    print(f"  Tamaño total: {total_size / (1024*1024):.2f} MB")
    print(f"  Ubicacion: {DEST}")
    print("=" * 60)

    # Listar estructura principal
    print("\nEstructura principal:")
    for item in sorted(os.listdir(DEST)):
        item_path = os.path.join(DEST, item)
        if os.path.isdir(item_path):
            count = sum(len(files) for _, _, files in os.walk(item_path))
            print(f"  📁 {item}/ ({count} archivos)")
        else:
            print(f"  📄 {item}")

if __name__ == "__main__":
    main()
