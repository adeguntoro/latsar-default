                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Type</strong>
                                        <span class="badge bg-secondary">{{ $post->type ?? 'N/A' }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Status</strong>
                                        <span class="badge bg-{{ $post->status == 'published' ? 'success' : ($post->status == 'draft' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($post->status ?? 'N/A') }}
                                        </span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Published At</strong>
                                        <span>{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->format('F d, Y H:i') : 'Not published' }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Created At</strong>
                                        <span>{{ $post->created_at->format('F d, Y H:i') }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Last Updated</strong>
                                        <span>{{ $post->updated_at->format('F d, Y H:i') }}</span>
                                    </div>

                                    <hr>

                                    <h6 class="border-bottom pb-2 mb-3">Statistics</h6>
                                    
                                    <div class="mb-2">
                                        <strong class="d-block text-muted small">Views</strong>
                                        <span class="badge bg-primary">{{ $post->views_count ?? 0 }}</span>
                                    </div>

                                    <div class="mb-3">
                                        <strong class="d-block text-muted small">Downloads</strong>
                                        <span class="badge bg-success">{{ $post->downloads_count ?? 0 }}</span>
                                    </div>

                                    @if($post->file_name)
                                        <hr>
                                        <h6 class="border-bottom pb-2 mb-3">Attached File</h6>
                                        
                                        <div class="alert alert-info py-2 px-3 small mb-2">
                                            <strong class="d-block">{{ $post->file_name }}</strong>
                                            <div class="mt-1">
                                                <span class="d-block">Type: {{ $post->file_type }}</span>
                                                <span class="d-block">Size: {{ number_format($post->file_size / 1024, 2) }} KB</span>
                                            </div>
                                        </div>

                                        @if(!empty($post->file_path))
                                            <a href="{{ Storage::url($post->file_path) }}" 
                                               class="btn btn-sm btn-success w-100" 
                                               download>
                                                <i class="bi bi-download"></i> Download File
                                            </a>
                                        @endif
                                    @endif