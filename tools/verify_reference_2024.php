<?php
/** Validasi offline tanpa sambungan database. Jalankan: php tools/verify_reference_2024.php */
declare(strict_types=1);
require __DIR__ . '/../app/Services/SpreadsheetReader.php';
require __DIR__ . '/../app/Services/SpkImportService.php';
require __DIR__ . '/../app/Services/EntropyTopsisEngine.php';
use App\Services\SpkImportService;
use App\Services\EntropyTopsisEngine;
$base = dirname(__DIR__);
$importer = new SpkImportService();
if (class_exists(\ZipArchive::class) && class_exists(\DOMDocument::class)) {
    $report = $importer->parse($base . '/data_source/OLAH_DATA_REPORT_SUKSES_2024.xlsx', 'report_2024');
    $actual = $importer->parse($base . '/data_source/OLAH_DATA_ACTUAL_REFUND_2024.xlsx', 'actual');
    $mode = 'REAL_XLSX';
} else {
    // Cadangan untuk PHP CLI yang tidak punya ekstensi ZIP/DOM: isi worksheet
    // diekstrak sebelumnya dari dua berkas Excel olahan dan disimpan sebagai JSON.
    $workbooks = json_decode((string) file_get_contents(__DIR__.'/reference_2024_workbook.json'), true);
    $report = (new ReflectionMethod(SpkImportService::class, 'parseReport2024'))->invoke($importer, $workbooks['report']);
    $actual = (new ReflectionMethod(SpkImportService::class, 'parseActual'))->invoke($importer, $workbooks['actual']);
    $mode = 'JSON_EXTRACTED_XLSX';
}
if (count($report['records']) !== 3473 || count($actual['records']) !== 174) {
    fwrite(STDERR, 'Jumlah input Excel tidak cocok: '.count($report['records']).' / '.count($actual['records'])."\n");
    exit(1);
}
$keys = ['FABLE','JURITA','GSB','KAIROS'];
$periods = ['P1'=>['2024-09-01','2024-10-31'],'P2'=>['2024-11-01','2024-12-31']];
$counts = [];
$matrix = [];
foreach ($keys as $key) {
    $summary = [];
    foreach ($periods as $period => [$start,$end]) {
        $tx = []; $ref=[];
        foreach ($report['records'] as $row) {
            if ($row['payer_key']===$key && $row['transaction_date'] >= $start && $row['transaction_date'] <= $end) {$tx[]=$row;}
        }
        foreach ($actual['records'] as $row) {
            if ($row['payer_key']===$key && $row['transaction_date'] >= $start && $row['transaction_date'] <= $end) {$ref[]=$row;}
        }
        $uniq = static function(array $rows,callable $fn): int {
            $map=[];
            foreach($rows as $r) {$value=$fn($r); if($value!=='') {$map[$value]=true;}}
            return count($map);
        };
        $summary[$period]=[
            'transactions'=>count($tx),
            'days'=>$uniq($tx,static fn($r):string=> (string)$r['transaction_date']),
            'countries'=>$uniq($tx,static fn($r):string=> strtoupper(trim((string)$r['beneficiary_country']))),
            'banks'=>$uniq($tx,static function($r):string {
                $s=trim((string)$r['swift']);
                return $s!=='' ? $s : strtoupper(trim((string)$r['beneficiary_bank']));
            }),
            'currencies'=>$uniq($tx,static fn($r):string=>strtoupper(trim((string)$r['currency']))),
            'refunds'=>$uniq($ref,static fn($r):string=> $r['payer_key'].'|'.(trim((string)$r['reference_no']) ?: $r['source_hash']).'|'.$r['transaction_date'])
        ];
    }
    $p1=$summary['P1'];$p2=$summary['P2'];
    $matrix[]=[
       $p2['transactions']/$p1['transactions'],
       ($p2['transactions']/$p2['days'])/($p1['transactions']/$p1['days']),
       $p2['countries']/$p1['countries'],
       $p2['banks']/$p1['banks'],
       $p2['currencies']/$p1['currencies'],
       1-$p2['refunds']/$p2['transactions']
    ];
    $counts[$key]=$summary;
}
$expectedTotals=[
 'FABLE'=>[1069,1375,71,43],
 'JURITA'=>[162,417,15,19],
 'GSB'=>[172,203,13,11],
 'KAIROS'=>[32,43,2,0]
];
foreach ($expectedTotals as $key=>[$p1,$p2,$r1,$r2]) {
    $v=$counts[$key];
    if([$v['P1']['transactions'],$v['P2']['transactions'],$v['P1']['refunds'],$v['P2']['refunds']] !== [$p1,$p2,$r1,$r2]) {
        fwrite(STDERR,"Mismatch agregasi: $key\n"); exit(1);
    }
}
// Nilai dalam kolom auto_value DB dibulatkan pada 8 digit, ikuti perilaku runtime.
$matrix = array_map(static fn($row):array=>array_map(static fn($x):float=>(float)number_format($x,8,'.',''),$row),$matrix);
$o=(new EntropyTopsisEngine())->calculate($matrix,array_fill(0,6,'benefit'));
$expectedW=[0.443079,0.196102,0.055691,0.249139,0.054239,0.001750];
$expectedV=[0.150054,0.999802,0.049516,0.182699];
foreach($expectedW as $j=>$v) {if(abs($v-$o['entropy']['weights'][$j])>0.00000051) {fwrite(STDERR,"Mismatch weight C".($j+1)."\n");exit(1);}}
foreach($expectedV as $j=>$v) {if(abs($v-$o['topsis']['preferences'][$j])>0.00000051) {fwrite(STDERR,"Mismatch TOPSIS index $j\n");exit(1);}}
if($o['topsis']['ranks'] !== [3,1,4,2]) {fwrite(STDERR,"Mismatch ranking\n");exit(1);}
echo json_encode(['status'=>'PASS','mode'=>$mode,'report_rows'=>count($report['records']),'refund_rows'=>count($actual['records']),'summary'=>$counts,'entropy_weights'=>$o['entropy']['weights'],'preferences'=>$o['topsis']['preferences'],'ranks'=>$o['topsis']['ranks']],JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE).PHP_EOL;
