<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(rand(3, 8));
        $slug = Str::slug($title);
        $content = fake()->paragraphs(rand(5, 15), true);
        
        // Generate a unique filename
        $fileName = $slug . '-' . Str::random(8) . '.pdf';
        $filePath = 'posts/' . $fileName;
        
        // Create directory if it doesn't exist
        if (!Storage::exists('posts')) {
            Storage::makeDirectory('posts');
        }
        
        // Generate PDF content
        $pdfContent = view('pdf.post', [
            'title' => $title,
            'content' => $content,
            'author' => fake()->name(),
            'date' => fake()->date()
        ])->render();
        
        $pdf = Pdf::loadHTML($pdfContent);
        $pdfOutput = $pdf->output();
        
        // Save PDF to storage
        Storage::put($filePath, $pdfOutput);
        
        $fileSize = strlen($pdfOutput);
        
        return [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => Str::limit($content, 200),
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => 'application/pdf',
            'file_size' => $fileSize,
            // 'short_url' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6)), //Str::random(6)
            // 'short_url' => strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8)), //Str::random(6)
            'short_url' => strtoupper(Str::random(8)),
            // 'type' => fake()->randomElement(['article', 'document', 'report', 'guide']), //default
            // 'type' => fake()->randomElement(['publik', 'internal', 'rahasia']),
            // 'type' => fake()->randomElement(config('post.types')),
            'type' => fake()->randomElement(config('temukpu.types')),
            //'department' => fake()->randomElement(['Rendatin', 'Kul', 'Komisioner', 'Teknis', 'SDM', 'Parmas']),//'rendatin',
            // 'department' => fake()->randomElement(config('post.departments')),
            'department' => fake()->randomElement(config('temukpu.departments')),

            'status' => fake()->randomElement(['published', 'draft']),
            'published_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
            'views_count' => '0', //fake()->numberBetween(0, 10000), //default
            'downloads_count' => '0', //fake()->numberBetween(0, 5000), //default
            'is_featured' => fake()->boolean(20),
        ];
    }
}
