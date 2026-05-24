import zipfile
import xml.etree.ElementTree as ET
import os

def extract_docx(docx_path, output_path):
    ns = '{http://schemas.openxmlformats.org/wordprocessingml/2006/main}'
    
    if not os.path.exists(docx_path):
        print(f"File not found: {docx_path}")
        return
        
    try:
        zf = zipfile.ZipFile(docx_path)
    except Exception as e:
        print(f"Failed to open zip: {e}")
        return
        
    try:
        xml_content = zf.read('word/document.xml')
    except Exception as e:
        print(f"Failed to read word/document.xml: {e}")
        zf.close()
        return
        
    zf.close()
    
    root = ET.fromstring(xml_content)
    
    out_file = open(output_path, 'w', encoding='utf-8')
    
    def walk_tree(element):
        # We look for paragraphs w:p and tables w:tbl at the top or recursive level
        for child in element:
            if child.tag == ns + 'p':
                # Paragraph
                p_text = "".join(t.text for t in child.iter(ns + 't') if t.text)
                if p_text.strip():
                    out_file.write(p_text + "\n\n")
            elif child.tag == ns + 'tbl':
                # Table
                out_file.write("--- TABLE START ---\n")
                for row in child.iter(ns + 'tr'):
                    row_cells = []
                    for cell in row.iter(ns + 'tc'):
                        cell_text = "".join(t.text for t in cell.iter(ns + 't') if t.text)
                        row_cells.append(cell_text.strip())
                    out_file.write(" | ".join(row_cells) + "\n")
                out_file.write("--- TABLE END ---\n\n")
            else:
                # Recurse
                walk_tree(child)
                
    walk_tree(root)
    out_file.close()
    print(f"Successfully extracted text to {output_path}")

if __name__ == '__main__':
    extract_docx('MediVault_Feature_Spec_v2 (1).docx', 'spec.txt')
