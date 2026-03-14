<?php

namespace Database\Factories;

use App\Models\RequestFile;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RequestFile>
 */
class RequestFileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Get random post that has a file
        $post = Post::whereNotNull('file_path')->inRandomOrder()->first();
        
        if (!$post) {
            // If no posts with files exist, create one
            $post = Post::factory()->create();
        }

        // Indonesian names
        $namaPeminta = $this->faker->randomElement([
            'Ahmad Rizki',
            'Budi Santoso',
            'Citra Dewi',
            'Dian Prasetyo',
            'Eka Nurul',
            'Firman Hidayat',
            'Gita Permata',
            'Hendra Wijaya',
            'Indah Lestari',
            'Joko Susilo',
            'Kurniawan Adi',
            'Lestari Wulan',
            'Muhammad Ali',
            'Nina Anggraini',
            'Oktavian Rudi',
            'Putri Sari',
            'Rudi Hartono',
            'Siti Aminah',
            'Teguh Wibowo',
            'Umi Kulsum',
            'Vera Agustina',
            'Wahyu Priyanto',
            'Yanti Mariana',
            'Zainal Abidin'
        ]);

        // Indonesian phone numbers
        $nomorTelepon = $this->faker->randomElement([
            '0812-1234-5678',
            '0856-9876-5432',
            '0878-1111-2222',
            '0895-3333-4444',
            '0813-5555-6666',
            '0821-7777-8888',
            '0838-9999-0000',
            '0852-1234-5678',
            '0819-8765-4321',
            '0877-2468-1357'
        ]);

        // Indonesian addresses
        $alamatPeminta = $this->faker->randomElement([
            'Jl. Merdeka No. 123, Jakarta Pusat',
            'Jl. Sudirman Kav. 45, Jakarta Selatan',
            'Jl. Thamrin No. 88, Jakarta Pusat',
            'Jl. Gatot Subroto No. 21, Jakarta Selatan',
            'Jl. Kuningan Barat No. 56, Jakarta Selatan',
            'Jl. Rasuna Said No. 12, Jakarta Selatan',
            'Jl. MH Thamrin No. 78, Jakarta Pusat',
            'Jl. Diponegoro No. 34, Jakarta Pusat',
            'Jl. Proklamasi No. 90, Jakarta Timur',
            'Jl. Jenderal Sudirman No. 100, Jakarta Pusat',
            'Jl. Asia Afrika No. 65, Jakarta Selatan',
            'Jl. Pintu Air No. 8, Jakarta Barat',
            'Jl. S. Parman No. 251, Jakarta Barat',
            'Jl. Letjen Supeno No. 15, Jakarta Timur',
            'Jl. Raya Bogor No. 234, Jakarta Timur'
        ]);

        // Reasons for request (Indonesian)
        $alasanPermintaan = $this->faker->randomElement([
            'Mohon dokumen untuk keperluan administrasi perkantoran',
            'Diperlukan sebagai referensi kerja',
            'Untuk pelaporan bulanan',
            'Mohon data untuk analisis dan pengambilan keputusan',
            'Keperluan audit internal',
            'Untuk disertakan dalam presentasi',
            'Diperlukan sebagai bahan pertimbangan',
            'Mohon dokumen untuk arsip',
            'Keperluan rapat koordinasi',
            'Untuk pendataan dan inventarisasi',
            'Diperlukan oleh divisi terkait',
            'Mohon data untuk perencanaan',
            'Keperluan evaluasi kinerja',
            'Untuk keperluan compliance',
            'Mohon dokumen untuk distribusi ke stakeholder'
        ]);

        // Random user who served the request (optional)
        $userServed = User::inRandomOrder()->first();

        return [
            'post_id' => $post->id,
            'nama_peminta' => $namaPeminta,
            'nomor_telepon' => $nomorTelepon,
            'alamat_peminta' => $alamatPeminta,
            'alasan_permintaan' => $alasanPermintaan,
            'file_path' => $post->file_path,
            'file_name' => $post->file_name,
            'file_type' => $post->file_type,
            'file_size' => $post->file_size,
            'user_served' => $userServed ? $userServed->id : null,
            'created_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'deleted_at' => $this->faker->optional(0.2, null)->dateTimeBetween('-3 months', 'now')
        ];
    }
}