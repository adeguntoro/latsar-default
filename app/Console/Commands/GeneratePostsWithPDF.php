<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Post;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class GeneratePostsWithPDF extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:generate {count=50 : Number of posts to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate dummy posts with PDF files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->argument('count');
        
        $this->info("Generating {$count} posts with PDF files...");
        
        // Create directory if it doesn't exist
        if (!Storage::exists('posts')) {
            Storage::makeDirectory('posts');
            $this->info('Created posts directory');
        }
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();
        
        for ($i = 0; $i < $count; $i++) {
            try {
                $title = fake()->sentence(rand(3, 8));
                $slug = Str::slug($title);
                $content = fake()->paragraphs(rand(5, 15), true);
                
                // Generate a unique filename
                $fileName = $slug . '-' . Str::random(8) . '.pdf';
                $filePath = 'posts/' . $fileName;
                
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
                
                // Create post
                Post::create([
                    'title' => $title,
                    'slug' => $slug,
                    'content' => $content,
                    'excerpt' => Str::limit($content, 200),
                    'file_name' => $fileName,
                    'file_path' => $filePath,
                    'file_type' => 'application/pdf',
                    'file_size' => $fileSize,
                    'type' => fake()->randomElement(['article', 'document', 'report', 'guide']),
                    'status' => fake()->randomElement(['published', 'draft']),
                    'published_at' => fake()->optional(0.8)->dateTimeBetween('-1 year', 'now'),
                    'views_count' => fake()->numberBetween(0, 10000),
                    'downloads_count' => fake()->numberBetween(0, 5000),
                    'is_featured' => fake()->boolean(20),
                ]);
                
                $bar->advance();
            } catch (\Exception $e) {
                $this->error("\nError creating post: " . $e->getMessage());
            }
        }
        
        $bar->finish();
        
        $this->newLine(2);
        $this->info("Successfully generated {$count} posts with PDF files!");
        $this->info("Total posts in database: " . Post::count());
        
        return Command::SUCCESS;
    }
}
