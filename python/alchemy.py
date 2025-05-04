from sqlalchemy import create_engine, inspect
import json

# ⚙️ CONFIGURA TU CONEXIÓN AQUÍ
DB_USER = 'root'
DB_PASS = 'root'
DB_HOST = 'localhost'
DB_NAME = 'hms-saas'

# Crear engine de conexión a MySQL
engine = create_engine(f"mysql+pymysql://{DB_USER}:{DB_PASS}@{DB_HOST}/{DB_NAME}")
inspector = inspect(engine)

def inspect_database():
    db_structure = {}

    for table_name in inspector.get_table_names():
        columns = inspector.get_columns(table_name)
        primary_keys = inspector.get_pk_constraint(table_name).get("constrained_columns", [])
        foreign_keys = inspector.get_foreign_keys(table_name)

        db_structure[table_name] = {
            "columns": [
                {"name": col["name"], "type": str(col["type"])} for col in columns
            ],
            "primary_keys": primary_keys,
            "foreign_keys": [
                {
                    "column": fk["constrained_columns"],
                    "referred_table": fk["referred_table"],
                    "referred_columns": fk["referred_columns"]
                } for fk in foreign_keys
            ]
        }

    return db_structure

if __name__ == "__main__":
    db_info = inspect_database()
    print(json.dumps(db_info, indent=4))
