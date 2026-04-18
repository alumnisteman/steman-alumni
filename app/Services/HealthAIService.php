<?php

namespace App\Services;

class HealthAIService
{
    protected AIService $ai;

    public function __construct(AIService $ai)
    {
        $this->ai = $ai;
    }

    /**
     * AI Gaya Hidup Sehat
     */
    public function analyzeLifestyle(array $data): ?string
    {
        $prompt = "Kamu adalah asisten edukasi kesehatan. 
        Analisis profil berikut (tujuan untuk edukasi gaya hidup sehat):
        Berat Badan: {$data['weight']} kg
        Tinggi Badan: {$data['height']} cm
        Kategori BMI: {$data['bmi_category']}
        Level Aktivitas: {$data['activity_level']}
        
        Berikan 3 rekomendasi singkat dan praktis mencakup: olahraga, pola makan, dan tidur.
        WAJIB akhiri dengan kalimat: '\n\n*Catatan: Rekomendasi ini hanya edukasi umum dan tidak menggantikan konsultasi medis profesional.*'";

        $result = $this->ai->ask($prompt, 0.5);

        if ($result) return $result;

        // Static Fallback for stability
        $fallback = "Berdasarkan profil Anda ({$data['bmi_category']}):\n";
        $fallback .= "1. Olahraga: Lakukan aktivitas fisik minimal 30 menit sehari (jalan cepat atau bersepeda).\n";
        $fallback .= "2. Pola Makan: Perbanyak konsumsi sayur, buah, dan kurangi asupan gula/garam berlebih.\n";
        $fallback .= "3. Istirahat: Pastikan tidur cukup 7-8 jam per hari untuk regenerasi tubuh.";
        $fallback .= "\n\n*Catatan: Rekomendasi ini adalah tips umum karena layanan AI sedang sibuk. Tetap konsultasikan dengan dokter.*";
        
        return $fallback;
    }

    /**
     * AI Early Warning Check
     */
    public function earlyWarningCheck(string $symptoms): ?string
    {
        $prompt = "Kamu adalah asisten edukasi kesehatan (Early Warning System). 
        Seseorang mengeluhkan gejala berikut: '{$symptoms}'.
        
        Berikan tanggapan empatik dalam 2-3 kalimat singkat. 
        DILARANG memberikan diagnosis pasti atau meresepkan obat.
        WAJIB diakhiri persis dengan kalimat: '\n\n*⚠️ Sebaiknya cek ke dokter untuk pemeriksaan lebih lanjut. Fitur ini hanya untuk edukasi dan BUKAN diagnosis medis.*'";

        $result = $this->ai->ask($prompt, 0.3);

        if ($result) return $result;

        return "Kami memahami kekhawatiran Anda terkait gejala tersebut. Karena keluhan kesehatan bersifat personal, sangat penting bagi Anda untuk mendapatkan pemeriksaan fisik langsung.\n\n*⚠️ Sebaiknya cek ke dokter untuk pemeriksaan lebih lanjut. Fitur ini hanya untuk edukasi dan BUKAN diagnosis medis.*";
    }

    /**
     * Chatbot Kesehatan
     */
    public function chat(string $message): ?string
    {
        $systemPrompt = "Kamu adalah 'Steman Health AI', asisten gaya hidup sehat untuk alumni (terutama usia 40+).
        ATURAN KERAS DAN MUTLAK:
        1. KAMU BUKAN DOKTER. DILARANG KERAS MENDIAGNOSIS ATAU MERESEPKAN OBAT MEDIS.
        2. Jika ditanya tentang penyakit spesifik, berikan penjelasan umum lalu SURUH KE DOKTER.
        3. Gunakan bahasa Indonesia yang hangat, sopan, dan memotivasi.
        4. Jika relevan, berikan tips aman untuk orang usia 40 tahun ke atas.
        5. Jawaban harus singkat, maksimal 3-4 kalimat.";

        $fullPrompt = "$systemPrompt\n\nUser: $message\nSteman Health AI:";
        
        $result = $this->ai->ask($fullPrompt, 0.6);
        
        if ($result) return $result;
        
        // Meaningful fallback based on common issues
        return "Mohon maaf, asisten AI saat ini sedang memiliki keterbatasan kapasitas. Untuk pertanyaan medis mendesak, segera hubungi dokter atau puskesmas terdekat. 🏥";
    }

}
