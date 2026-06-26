<?php
use App\Models\Major;
use App\Models\Classroom;

// Update existing records to avoid foreign key constraints errors if possible
// Map MM to TBSM
$mm = Major::where('code', 'MM')->first();
if ($mm) {
    $mm->update([
        'code' => 'TBSM',
        'name' => 'Teknik Bisnis Sepeda Motor'
    ]);
} else {
    Major::firstOrCreate(['code' => 'TBSM'], ['name' => 'Teknik Bisnis Sepeda Motor']);
}

// Get AKL and TKJ just to be sure they exist
Major::firstOrCreate(['code' => 'AKL'], ['name' => 'Akuntansi dan Keuangan Lembaga']);
Major::firstOrCreate(['code' => 'TKJ'], ['name' => 'Teknik Komputer Jaringan']);

// Re-assign classrooms that belong to OTKP or RPL to TKJ (fallback)
$tkj = Major::where('code', 'TKJ')->first();

$otkp = Major::where('code', 'OTKP')->first();
if ($otkp) {
    Classroom::where('major_id', $otkp->id)->update(['major_id' => $tkj->id]);
    $otkp->delete();
}

$rpl = Major::where('code', 'RPL')->first();
if ($rpl) {
    Classroom::where('major_id', $rpl->id)->update(['major_id' => $tkj->id]);
    $rpl->delete();
}

echo "Majors updated successfully.\n";
