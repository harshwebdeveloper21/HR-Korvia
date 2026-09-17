import os
import re

directory = r'd:\xampp\htdocs\HR\fableadhrportal\app\Views'

# Matches the standard $.each block that adds validation errors
pattern = re.compile(
    r'\$\.each\(\s*([a-zA-Z0-9_\.]+)\s*,\s*function\s*\(\s*[a-zA-Z0-9_]+\s*,\s*[a-zA-Z0-9_]+\s*\)\s*\{'
    r'\s*let\s+inputField\s*=\s*\$\(\s*`\[name="\$\{[^}]+\}"\]`\s*\);'
    r'\s*if\s*\(inputField\.length\)\s*\{'
    r'\s*inputField\.addClass\(\'is-invalid\'\);'
    r'(?:\s*inputField\.next\(\'\.invalid-feedback\'\)\.remove\(\);)?'
    r'\s*inputField\.after\(.*?\);'
    r'\s*\}'
    r'\s*\}\);', re.DOTALL)

# Matches variations where .next().remove() might be missing, or slightly different spaces
pattern2 = re.compile(
    r'\$\.each\(\s*([a-zA-Z0-9_\.]+)\s*,\s*function\s*\([^)]+\)\s*\{'
    r'[^}]*?let\s+inputField\s*=\s*\$\([^)]+\);'
    r'[^}]*?inputField\.addClass\([^\)]+\);'
    r'[^}]*?inputField\.after\([^\)]+\);'
    r'[^}]*?\}\);', re.DOTALL)

count = 0
for root, dirs, files in os.walk(directory):
    for file in files:
        if file.endswith('.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content
            
            # Use pattern
            for match in pattern.finditer(content):
                original_text = match.group(0)
                errors_var = match.group(1)
                replacement = f"if (typeof displayValidationErrors === 'function') displayValidationErrors({errors_var});"
                new_content = new_content.replace(original_text, replacement)
                
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                print(f"Updated {filepath} (pattern 1)")
                count += 1

print(f"Total files updated: {count}")
