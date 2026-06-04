import re

print("Reading backup.sql...")
with open('/tmp/steman_backup/backup.sql', 'r', encoding='utf-8', errors='ignore') as f:
    content = f.read()

# Find the block for reuni_steman
print("Searching for reuni_steman database block...")
start = content.find('Current Database: `reuni_steman`')
if start == -1:
    start = content.find('Current Database: reuni_steman')

if start != -1:
    # Find where the actual SQL statements start after USE `reuni_steman`;
    use_idx = content.find('USE `reuni_steman`;', start)
    if use_idx == -1:
        use_idx = content.find('use reuni_steman;', start)
    
    if use_idx != -1:
        start_sql = use_idx + len('USE `reuni_steman`;')
    else:
        start_sql = start
        
    # Find the next database start
    next_db = re.search(r'-- Current Database: `(?!reuni_steman`)\w+`', content[start+30:])
    if next_db:
        end = start + 30 + next_db.start()
    else:
        end = len(content)
    
    print(f"Extracted block size: {end - start_sql} characters")
    # We will write the SQL without the USE statement, so it can be imported into steman_alumni
    with open('/tmp/steman_alumni_extracted.sql', 'w', encoding='utf-8') as out:
        out.write(content[start_sql:end])
    print('Extraction successful!')
else:
    print('reuni_steman not found in dump!')
