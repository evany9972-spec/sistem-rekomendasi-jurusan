<?php
// ============================================
// ALGORITMA PROFILE MATCHING
// ============================================

/**
 * Tabel konversi GAP ke nilai bobot
 * GAP = Nilai Siswa - Nilai Ideal Jurusan
 */
function getBobot($gap) {
    $tabel = [
        0  => 5,    // Tidak ada selisih (kompetensi sesuai)
        1  => 4.5,  // Kompetensi individu kelebihan 1 tingkat
        -1 => 4,    // Kompetensi individu kekurangan 1 tingkat
        2  => 3.5,  // Kompetensi individu kelebihan 2 tingkat
        -2 => 3,    // Kompetensi individu kekurangan 2 tingkat
        3  => 2.5,  // Kompetensi individu kelebihan 3 tingkat
        -3 => 2,    // Kompetensi individu kekurangan 3 tingkat
        4  => 1.5,  // Kompetensi individu kelebihan 4 tingkat
        -4 => 1,    // Kompetensi individu kekurangan 4 tingkat
    ];

    // Normalisasi gap (skala nilai berbeda dengan skala bobot)
    $gapNorm = round($gap / 10); // untuk nilai 0-100, bagi 10

    if (isset($tabel[$gapNorm])) {
        return $tabel[$gapNorm];
    } elseif ($gapNorm > 4) {
        return 1.5;
    } else {
        return 1;
    }
}

/**
 * Hitung Profile Matching untuk satu siswa terhadap semua jurusan
 */
function hitungProfileMatching($conn, $siswa_id) {
    // Ambil semua jurusan
    $jurusanList = mysqli_query($conn, "SELECT * FROM jurusan");

    // Ambil nilai siswa
    $nilaiSiswa = [];
    $qNilai = mysqli_query($conn, "SELECT kriteria_id, nilai FROM nilai_siswa WHERE siswa_id = $siswa_id");
    while ($row = mysqli_fetch_assoc($qNilai)) {
        $nilaiSiswa[$row['kriteria_id']] = $row['nilai'];
    }

    // Ambil kriteria beserta tipe dan bobot
    $kriteriaList = [];
    $qKriteria = mysqli_query($conn, "SELECT * FROM kriteria");
    while ($row = mysqli_fetch_assoc($qKriteria)) {
        $kriteriaList[$row['id']] = $row;
    }

    // Hitung bobot CF dan SF
    $totalBobotCF = 0;
    $totalBobotSF = 0;
    foreach ($kriteriaList as $kr) {
        if ($kr['tipe'] == 'core_factor') $totalBobotCF += $kr['bobot'];
        else $totalBobotSF += $kr['bobot'];
    }

    $hasil = [];

    while ($jurusan = mysqli_fetch_assoc($jurusanList)) {
        $jid = $jurusan['id'];

        // Ambil profil ideal jurusan ini
        $profilIdeal = [];
        $qProfil = mysqli_query($conn, "SELECT * FROM profil_jurusan WHERE jurusan_id = $jid");
        while ($row = mysqli_fetch_assoc($qProfil)) {
            $profilIdeal[$row['kriteria_id']] = $row['nilai_ideal'];
        }

        // Hitung GAP dan bobot tiap kriteria
        $nilaiCF = 0;
        $nilaiSF = 0;
        $countCF = 0;
        $countSF = 0;
        $detailGap = [];

        foreach ($kriteriaList as $kid => $kr) {
            $nilaiS  = isset($nilaiSiswa[$kid]) ? $nilaiSiswa[$kid] : 0;
            $nilaiI  = isset($profilIdeal[$kid]) ? $profilIdeal[$kid] : 0;
            $gap     = $nilaiS - $nilaiI;
            $bobot   = getBobot($gap);

            $detailGap[$kid] = [
                'nama'   => $kr['nama_kriteria'],
                'nilai'  => $nilaiS,
                'ideal'  => $nilaiI,
                'gap'    => $gap,
                'bobot'  => $bobot,
                'tipe'   => $kr['tipe'],
            ];

            if ($kr['tipe'] == 'core_factor') {
                $nilaiCF += $bobot * ($kr['bobot'] / 100);
                $countCF++;
            } else {
                $nilaiSF += $bobot * ($kr['bobot'] / 100);
                $countSF++;
            }
        }

        // Normalisasi
        $pctCF = ($totalBobotCF > 0) ? ($totalBobotCF / 100) : 0;
        $pctSF = ($totalBobotSF > 0) ? ($totalBobotSF / 100) : 0;

        // Nilai akhir: 60% Core Factor + 40% Secondary Factor
        $ncf = ($pctCF > 0) ? ($nilaiCF / $pctCF) : 0;
        $nsf = ($pctSF > 0) ? ($nilaiSF / $pctSF) : 0;
        $nilaiTotal = (0.6 * $ncf) + (0.4 * $nsf);

        $hasil[] = [
            'jurusan_id'   => $jid,
            'nama_jurusan' => $jurusan['nama_jurusan'],
            'deskripsi'    => $jurusan['deskripsi'],
            'nilai_cf'     => round($ncf, 4),
            'nilai_sf'     => round($nsf, 4),
            'nilai_total'  => round($nilaiTotal, 4),
            'detail_gap'   => $detailGap,
        ];
    }

    // Urutkan dari nilai tertinggi
    usort($hasil, function($a, $b) {
        return $b['nilai_total'] <=> $a['nilai_total'];
    });

    // Tambahkan ranking
    foreach ($hasil as $i => &$h) {
        $h['ranking'] = $i + 1;
    }

    return $hasil;
}

/**
 * Simpan hasil ke database
 */
function simpanHasil($conn, $siswa_id, $hasilList) {
    // Hapus hasil lama
    mysqli_query($conn, "DELETE FROM hasil_rekomendasi WHERE siswa_id = $siswa_id");

    foreach ($hasilList as $h) {
        $jid   = $h['jurusan_id'];
        $ncf   = $h['nilai_cf'];
        $nsf   = $h['nilai_sf'];
        $total = $h['nilai_total'];
        $rank  = $h['ranking'];
        mysqli_query($conn, "INSERT INTO hasil_rekomendasi (siswa_id, jurusan_id, nilai_cf, nilai_sf, nilai_total, ranking)
            VALUES ($siswa_id, $jid, $ncf, $nsf, $total, $rank)");
    }
}
?>
