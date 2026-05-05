
import re

file_path = r'd:\xampp\htdocs\fableadhrportal\app\Views\payroll\view.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Check if already added
if 'generateSalarySheetPDF' in content:
    print("PDF function already present. Exiting.")
    exit(0)

pdf_script = """
<script>
    function generateSalarySheetPDF() {
        if (typeof window.jspdf === 'undefined') { alert('PDF library not loaded.'); return; }
        var payrolls = window._payrollData || [];
        if (!payrolls.length) { alert('No payroll records loaded yet. Please wait for the table to finish loading.'); return; }
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
        var rawMonth = document.getElementById('payrollMonthFilter') ? document.getElementById('payrollMonthFilter').value : '';
        var parts = (rawMonth || '2026-01').split('-');
        var yr = parts[0]; var mo = parseInt(parts[1]);
        var monthLabel = new Date(yr, mo - 1, 1).toLocaleString('en-IN', { month: 'long' }) + ' ' + yr;
        var fileName = 'Salary_Sheet_' + monthLabel.replace(' ', '_') + '.pdf';
        var R = String.fromCharCode(8377);
        var tableBody = []; var tL=0,tD=0,tS=0,tN=0;
        payrolls.forEach(function(p) {
            var name = (p.username||'').trim();
            var sal = parseFloat(p.salary_amount)||0;
            var net = parseFloat(p.net_salary)||0;
            var tax = parseFloat(p.tax_deduction)||0;
            var ded = Math.max(sal-net-tax, 0);
            var txT = tax > 0 ? R+tax.toLocaleString('en-IN',{minimumFractionDigits:2}) : 'No Tax';
            var lv  = parseFloat(p.total_leaves)||0;
            var wd  = parseFloat(p.working_days)||26;
            var pd  = wd > 0 ? sal/wd : 0;
            tL+=lv; tD+=ded; tS+=sal; tN+=net;
            var f = function(n){ return R+n.toLocaleString('en-IN',{minimumFractionDigits:2,maximumFractionDigits:2}); };
            tableBody.push([name, lv%1===0?lv.toString():lv.toFixed(1), f(pd), f(ded), f(sal), txT, f(net), f(net)]);
        });
        var ft = function(n){ return R+n.toLocaleString('en-IN',{minimumFractionDigits:2}); };
        tableBody.push(['TOTAL', tL%1===0?tL.toString():tL.toFixed(1), '', ft(tD), ft(tS), '', ft(tN), ft(tN)]);
        var or_=[230,97,54], dk=[40,40,50], lg=[248,248,248], wh=[255,255,255];
        var pw = doc.internal.pageSize.getWidth(); var ti = tableBody.length-1;
        doc.setFillColor(or_[0],or_[1],or_[2]); doc.rect(40,30,pw-80,22,'F');
        doc.setTextColor(255,255,255); doc.setFontSize(12); doc.setFont('helvetica','bold');
        doc.text('Fablead Developers Technolab', pw/2, 45, {align:'center'});
        doc.setFillColor(dk[0],dk[1],dk[2]); doc.rect(40,52,pw-80,18,'F');
        doc.setTextColor(255,255,255); doc.setFontSize(9); doc.setFont('helvetica','normal');
        doc.text('Monthly Salary Sheet - ' + monthLabel, pw/2, 64, {align:'center'});
        doc.autoTable({
            startY:74, margin:{left:40,right:40},
            head:[['NAME','LEAVE\\n(Days)','PER DAY\\nSALARY','DEDUCTION','SALARY','TAX','NET PAY','SALARY AMOUNT']],
            body:tableBody,
            headStyles:{fillColor:or_,textColor:wh,fontStyle:'bold',fontSize:7,halign:'center',valign:'middle',cellPadding:3},
            columnStyles:{
                0:{halign:'left',cellWidth:'auto'},1:{halign:'center',cellWidth:38},
                2:{halign:'right',cellWidth:65},   3:{halign:'right',cellWidth:65},
                4:{halign:'right',cellWidth:65},   5:{halign:'center',cellWidth:48},
                6:{halign:'right',cellWidth:65},   7:{halign:'right',cellWidth:72}
            },
            styles:{fontSize:7.5,cellPadding:{top:4,bottom:4,left:4,right:4},overflow:'linebreak',lineColor:[220,220,220],lineWidth:0.3},
            alternateRowStyles:{fillColor:lg},
            bodyStyles:{textColor:[30,30,30],valign:'middle'},
            didParseCell:function(data){
                if(data.section==='body'&&data.row.index===ti){
                    data.cell.styles.fillColor=dk; data.cell.styles.textColor=wh;
                    data.cell.styles.fontStyle='bold'; data.cell.styles.fontSize=8; return;
                }
                if(data.section==='body'&&data.column.index===3){
                    data.cell.styles.textColor=[200,0,0]; data.cell.styles.fontStyle='bold';
                }
                if(data.section==='body'&&(data.column.index===6||data.column.index===7)){
                    data.cell.styles.textColor=[0,150,70]; data.cell.styles.fontStyle='bold';
                }
            }
        });
        doc.save(fileName);
    }
</script>
"""

# Append before endSection
target = "<?= $this->endSection() ?>"
if target in content:
    content = content.replace(target, pdf_script + "\n" + target, 1)
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("Done: PDF function appended successfully.")
else:
    print("ERROR: endSection marker not found.")
