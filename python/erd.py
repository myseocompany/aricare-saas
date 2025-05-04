import re
import graphviz

class SQLERDGenerator:
    def __init__(self, sql_file):
        with open(sql_file, 'r', encoding='utf-8') as f:
            self.sql = f.read()
        self.tables = {}

    def parse(self):
        create_table_statements = re.findall(r'CREATE TABLE\s+`?(\w+)`?\s*\((.*?)\);', self.sql, re.DOTALL | re.IGNORECASE)
        for table_name, body in create_table_statements:
            self.tables[table_name] = self.parse_table_body(body)

    def parse_table_body(self, body):
        columns = []
        foreign_keys = []
        primary_keys = []

        lines = [line.strip().strip(',') for line in body.splitlines() if line.strip()]
        for line in lines:
            if line.upper().startswith('PRIMARY KEY'):
                pk_match = re.findall(r'\((.*?)\)', line)
                if pk_match:
                    primary_keys.extend(col.strip('` ') for col in pk_match[0].split(','))
            elif line.upper().startswith('FOREIGN KEY'):
                fk = re.search(r'FOREIGN KEY\s+\(`?(.*?)`?\)\s+REFERENCES\s+`?(.*?)`?\s+\(`?(.*?)`?\)', line, re.IGNORECASE)
                if fk:
                    foreign_keys.append({
                        'column': fk.group(1),
                        'ref_table': fk.group(2),
                        'ref_column': fk.group(3)
                    })
            elif re.match(r'`?\w+`?\s+\w+', line):  # Column definition
                parts = line.split()
                col_name = parts[0].strip('`')
                col_type = parts[1]
                columns.append({'name': col_name, 'type': col_type})

        return {
            'columns': columns,
            'primary_keys': primary_keys,
            'foreign_keys': foreign_keys
        }

    def generate_erd(self, output_file='erd_diagram'):
        dot = graphviz.Digraph(comment='ERD', format='png')
        
        for table, data in self.tables.items():
            label = f"<<TABLE BORDER='1' CELLBORDER='1' CELLSPACING='0'><TR><TD COLSPAN='2'><B>{table}</B></TD></TR>"
            for col in data['columns']:
                name = col['name']
                if name in data['primary_keys']:
                    label += f"<TR><TD>{name}</TD><TD><B>{col['type']}</B> (PK)</TD></TR>"
                else:
                    label += f"<TR><TD>{name}</TD><TD>{col['type']}</TD></TR>"
            label += "</TABLE>>"
            dot.node(table, label=label, shape='plain')

        for table, data in self.tables.items():
            for fk in data['foreign_keys']:
                dot.edge(table, fk['ref_table'], label=f"{fk['column']} → {fk['ref_column']}")
        
        dot.render(output_file, view=True)

# Uso:
if __name__ == '__main__':
    erd = SQLERDGenerator('../database/hms-saas.sql')
    erd.parse()
    erd.generate_erd('mi_erd')
