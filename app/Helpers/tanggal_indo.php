<?php

if (!function_exists('tanggal_indo')) {
    function tanggal_indo($tanggal): string
    {
        if (empty($tanggal)) return '-';
        try {
            $t = \Carbon\Carbon::parse($tanggal);
            $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return $t->day.' '.$bulan[$t->month].' '.$t->year;
        } catch (\Throwable $e) {
            $bulan = [1=>'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            $pecahkan = explode('-', substr((string)$tanggal,0,10));
            if (count($pecahkan)!==3) return (string)$tanggal;
            return ((int)$pecahkan[2]).' '.$bulan[(int)$pecahkan[1]].' '.$pecahkan[0];
        }
    }
}

if (!function_exists('hari_indo')) {
    function hari_indo($tanggal): string
    {
        $map = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
        try {
            $eng = \Carbon\Carbon::parse($tanggal)->format('l');
            return $map[$eng] ?? $eng;
        } catch (\Throwable $e) { return '-'; }
    }
}

if (!function_exists('tanggal_indo_full')) {
    function tanggal_indo_full($tanggal): string
    {
        if (empty($tanggal)) return '-';
        try {
            $t = \Carbon\Carbon::parse($tanggal);
            return hari_indo($tanggal).', '.$t->format('d').' '.explode(' ', tanggal_indo($tanggal))[1].' '.$t->year;
        } catch (\Throwable $e) { return (string)$tanggal; }
    }
}
