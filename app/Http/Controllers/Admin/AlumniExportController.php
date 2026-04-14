<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Response;

class AlumniExportController extends Controller
{
    public function export()
    {
        $alumni = User::where('role', 'alumni')->get();
        
        $filename = "data_alumni_" . date('Y-m-d') . ".xls";

        $html = '<html><head><meta charset="utf-8"></head><body>';
        $html .= '<h2>Laporan Data Alumni Steman</h2>';
        $html .= '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse: collapse;">';
        $html .= '<tr style="background-color:#3f37c9; color:#ffffff; font-weight:bold; text-align:center;">';
        $html .= '<th>ID</th><th>Nama Lengkap</th><th>Email</th><th>NISN</th><th>major</th><th>Tahun Lulus</th><th>Status / Pekerjaan</th><th>Tempat / Instansi</th><th>No. Telp</th><th>address Lengkap</th><th>Bio Singkat</th>';
        $html .= '</tr>';

        foreach ($alumni as $user) {
            $html .= '<tr>';
            $html .= '<td style="text-align:center;">' . $user->id . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->name) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->email) . '</td>';
            $html .= '<td style="mso-number-format:\'\@\'; text-align:center;">' . htmlspecialchars((string) $user->nisn) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->major) . '</td>';
            $html .= '<td style="text-align:center;">' . htmlspecialchars((string) $user->graduation_year) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->current_job) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->company_university) . '</td>';
            $html .= '<td style="mso-number-format:\'\@\';">' . htmlspecialchars((string) $user->phone_number) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->address) . '</td>';
            $html .= '<td>' . htmlspecialchars((string) $user->bio) . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table></body></html>';

        return response($html)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
