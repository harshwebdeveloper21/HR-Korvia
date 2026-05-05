<?php
$file = 'd:/xampp/htdocs/fableadhrportal/app/Views/payroll/view.php';
$content = file_get_contents($file);

if (strpos($content, 'generateSalarySheetPDF') !== false) {
    echo "Already present.\n";
    exit;
}

$pdf = '
<script>
    function generateSalarySheetPDF() {
        if (typeof window.jspdf === \'undefined\') { alert(\'PDF library not loaded.\'); return; }
        var payrolls = window._payrollData || [];
        if (!payrolls.length) { alert(\'No payroll records loaded yet.\'); return; }
        var jsPDF = window.jspdf.jsPDF;
        var doc = new jsPDF({ orientation: \'landscape\', unit: \'pt\', format: \'a4\' });
        var monthEl = document.getElementById(\'payrollMonthFilter\');
        var rawMonth = monthEl ? monthEl.value : \'2026-01\';
        var parts = (rawMonth || \'2026-01\').split(\'-\');
        var yr = parts[0]; var mo = parseInt(parts[1]);
        var monthLabel = new Date(yr, mo-1, 1).toLocaleString(\'en-IN\',{month:\'long\'})+\' \'+yr;
        var fileName = \'Salary_Sheet_\'+monthLabel.replace(\' \',\'_\')+\'.pdf\';
        var R = String.fromCharCode(8377);
        var body=[]; var tL=0,tD=0,tS=0,tN=0;
        payrolls.forEach(function(p){
            var s=parseFloat(p.salary_amount)||0, n=parseFloat(p.net_salary)||0, tx=parseFloat(p.tax_deduction)||0;
            var d=Math.max(s-n-tx,0), lv=parseFloat(p.total_leaves)||0, wd=parseFloat(p.working_days)||26, pd=wd>0?s/wd:0;
            tL+=lv; tD+=d; tS+=s; tN+=n;
            var f=function(v){return R+v.toLocaleString(\'en-IN\',{minimumFractionDigits:2,maximumFractionDigits:2});};
            body.push([(p.username||\'\'),(lv%1===0?lv.toString():lv.toFixed(1)),f(pd),f(d),f(s),(tx>0?R+tx.toLocaleString(\'en-IN\',{minimumFractionDigits:2}):\'No Tax\'),f(n),f(n)]);
        });
        var ft=function(v){return R+v.toLocaleString(\'en-IN\',{minimumFractionDigits:2});};
        body.push([\'TOTAL\',(tL%1===0?tL.toString():tL.toFixed(1)),\'\',ft(tD),ft(tS),\'\',ft(tN),ft(tN)]);
        var OR=[230,97,54],DK=[40,40,50],LG=[248,248,248],WH=[255,255,255];
        var pw=doc.internal.pageSize.getWidth(), ti=body.length-1;
        doc.setFillColor(OR[0],OR[1],OR[2]); doc.rect(40,30,pw-80,22,\'F\');
        doc.setTextColor(255,255,255); doc.setFontSize(12); doc.setFont(\'helvetica\',\'bold\');
        doc.text(\'Fablead Developers Technolab\',pw/2,45,{align:\'center\'});
        doc.setFillColor(DK[0],DK[1],DK[2]); doc.rect(40,52,pw-80,18,\'F\');
        doc.setTextColor(255,255,255); doc.setFontSize(9); doc.setFont(\'helvetica\',\'normal\');
        doc.text(\'Monthly Salary Sheet - \'+monthLabel,pw/2,64,{align:\'center\'});
        doc.autoTable({startY:74,margin:{left:40,right:40},
            head:[[\'NAME\',\'LEAVE\n(Days)\',\'PER DAY\nSALARY\',\'DEDUCTION\',\'SALARY\',\'TAX\',\'NET PAY\',\'SALARY AMOUNT\']],
            body:body,
            headStyles:{fillColor:OR,textColor:WH,fontStyle:\'bold\',fontSize:7,halign:\'center\',valign:\'middle\',cellPadding:3},
            columnStyles:{0:{halign:\'left\',cellWidth:\'auto\'},1:{halign:\'center\',cellWidth:38},2:{halign:\'right\',cellWidth:65},3:{halign:\'right\',cellWidth:65},4:{halign:\'right\',cellWidth:65},5:{halign:\'center\',cellWidth:48},6:{halign:\'right\',cellWidth:65},7:{halign:\'right\',cellWidth:72}},
            styles:{fontSize:7.5,cellPadding:{top:4,bottom:4,left:4,right:4},overflow:\'linebreak\',lineColor:[220,220,220],lineWidth:0.3},
            alternateRowStyles:{fillColor:LG},bodyStyles:{textColor:[30,30,30],valign:\'middle\'},
            didParseCell:function(data){
                if(data.section===\'body\'&&data.row.index===ti){data.cell.styles.fillColor=DK;data.cell.styles.textColor=WH;data.cell.styles.fontStyle=\'bold\';data.cell.styles.fontSize=8;return;}
                if(data.section===\'body\'&&data.column.index===3){data.cell.styles.textColor=[200,0,0];data.cell.styles.fontStyle=\'bold\';}
                if(data.section===\'body\'&&(data.column.index===6||data.column.index===7)){data.cell.styles.textColor=[0,150,70];data.cell.styles.fontStyle=\'bold\';}
            }
        });
        doc.save(fileName);
    }
</script>
';

$endSection = '<?= $this->endSection() ?>';
$content = str_replace($endSection, $pdf . "\n" . $endSection, $content);
file_put_contents($file, $content);
echo "Done\n";
