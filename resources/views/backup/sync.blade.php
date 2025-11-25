@extends('layouts.dashboard')
@section('title', 'Sync Database')
@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <section class="sync-hero">
        <div>
            <p class="text-sm uppercase tracking-wider text-white/80">Database Sync · Restore</p>
            <h1 class="text-3xl font-bold mt-2">Recover production data without the guesswork</h1>
            <p class="mt-3 text-white/85 text-base max-w-2xl">Upload a verified SQL dump, walk through the safety checklist, and let the system restore everything inside a protected transaction.</p>
            <div class="mt-4 flex flex-wrap gap-3">
                <span class="sync-pill"><i class="fas fa-file-signature mr-2"></i>SQL or TXT only</span>
                <span class="sync-pill"><i class="fas fa-weight-hanging mr-2"></i>50&nbsp;MB max</span>
                <span class="sync-pill"><i class="fas fa-undo-alt mr-2"></i>Full rollback on failure</span>
            </div>
        </div>
        <div class="sync-hero-stats">
            <div>
                <p class="label">Last backup</p>
                <p class="value">{{ session('last_backup_at') ?? 'Not available' }}</p>
                <span class="sub">Run backup before syncing</span>
            </div>
            <div>
                <p class="label">Estimated duration</p>
                <p class="value">&lt; 2 min</p>
                <span class="sub">Depends on dump size</span>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
        <div class="grid gap-4 md:grid-cols-4">
            <div class="sync-step sync-step--complete">
                <div class="sync-step__icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <p class="title">Prepare</p>
                    <p class="description">Verify latest backup + maintenance window.</p>
                </div>
            </div>
            <div class="sync-step sync-step--active">
                <div class="sync-step__icon"><i class="fas fa-file-upload"></i></div>
                <div>
                    <p class="title">Upload</p>
                    <p class="description">Select the SQL dump to restore.</p>
                </div>
            </div>
            <div class="sync-step">
                <div class="sync-step__icon"><i class="fas fa-tasks"></i></div>
                <div>
                    <p class="title">Validate</p>
                    <p class="description">Schema + FK checks, per statement logging.</p>
                </div>
            </div>
            <div class="sync-step">
                <div class="sync-step__icon"><i class="fas fa-database"></i></div>
                <div>
                    <p class="title">Restore</p>
                    <p class="description">Transaction commit + confirmation.</p>
                </div>
            </div>
        </div>
    </section>

    @if($errors->any())
        <div class="sync-alert sync-alert--error">
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div>
                <p class="title">We could not finish the sync</p>
                <ul class="mt-2 space-y-1 text-sm text-red-900">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    @if(session('status'))
        <div class="sync-alert sync-alert--success">
            <div class="icon"><i class="fas fa-check-circle"></i></div>
            <div>
                <p class="title">{{ session('status') }}</p>
                <p class="text-green-900 text-sm mt-1">All statements executed successfully.</p>
            </div>
        </div>
    @endif

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">
            <div class="sync-card">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Upload + Restore</h2>
                        <p class="text-sm text-gray-500 mt-1">The system wraps the entire import in a single transaction.</p>
                    </div>
                    <form method="POST" action="{{ route('backup.download') }}">
                        @csrf
                        <button type="submit" class="sync-pill text-xs">
                            <i class="fas fa-download mr-2"></i>Download latest backup
                        </button>
                    </form>
                </div>

                <div class="sync-warning mt-5">
                    <i class="fas fa-exclamation-triangle"></i>
                    <div>
                        <p class="font-semibold text-amber-900">This action overwrites every table in the active database.</p>
                        <p class="text-sm text-amber-800">Only continue if you have written approval and a fresh backup file.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('backup.sync') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="syncForm()">
                    @csrf

                    <input type="file" id="sql_file" name="sql_file" accept=".sql,.txt" class="sr-only" x-ref="file" @change="handleFile($event)" required>

                    <label for="sql_file" class="sync-dropzone"
                           :class="{ 'sync-dropzone--ready': fileName, 'sync-dropzone--error': error, 'sync-dropzone--drag': dragActive }"
                           @click.prevent="$refs.file.click()"
                           @dragover.prevent="setDrag(true)"
                           @dragleave.prevent="setDrag(false)"
                           @drop.prevent="handleDrop($event)">
                        <div class="icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="flex-1 text-left">
                            <p class="font-semibold text-gray-900" x-text="fileName || 'Drag & drop or browse for your SQL dump'"></p>
                            <p class="text-sm text-gray-500">Accepted: .sql / .txt &middot; Max 50MB &middot; UTF-8 recommended</p>
                            <template x-if="error">
                                <p class="mt-2 text-sm text-red-600" x-text="error"></p>
                            </template>
                        </div>
                        <button type="button" class="sync-pill" @click.stop.prevent="$refs.file.click()">Browse</button>
                    </label>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="sync-summary">
                            <p class="label">What happens</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Disables FK checks until completion</li>
                                <li><i class="fas fa-check"></i> Executes statements sequentially</li>
                                <li><i class="fas fa-check"></i> Rolls back entire run on error</li>
                            </ul>
                        </div>
                        <div class="sync-summary">
                            <p class="label">Before you start</p>
                            <ul>
                                <li><i class="fas fa-check"></i> Confirm file is from trusted source</li>
                                <li><i class="fas fa-check"></i> Ensure no one is entering sales</li>
                                <li><i class="fas fa-check"></i> Communicate expected downtime</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                        <label class="flex items-start space-x-3">
                            <input type="checkbox" class="h-5 w-5 text-red-600 border-gray-300 rounded" x-model="confirmed" required>
                            <span class="text-sm text-red-800">
                                I understand that this sync permanently overwrites current data and I have secured a fallback backup.
                            </span>
                        </label>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-600 hover:text-gray-900">
                            <i class="fas fa-arrow-left mr-2"></i>Back to dashboard
                        </a>
                        <div class="flex items-center gap-3">
                            <button type="button" class="text-sm text-gray-500 hover:text-gray-700" @click="resetFile($event)" x-show="fileName">Clear selection</button>
                            <button type="submit" class="restore-btn inline-flex items-center rounded-lg px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition-all bg-green-600 hover:bg-green-700 disabled:opacity-40 disabled:cursor-not-allowed" :disabled="!confirmed || !fileName">
                                <span class="mr-2"><i class="fas fa-database"></i></span>Restore database
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-6">
            <div class="sync-card">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Safety checklist</h3>
                <ul class="sync-checklist">
                    <li><i class="fas fa-lock"></i>Restrict access to admin users only.</li>
                    <li><i class="fas fa-wifi"></i>Use a stable network connection (no VPN drops).</li>
                    <li><i class="fas fa-clipboard-list"></i>Keep a copy of the change ticket or approval note.</li>
                    <li><i class="fas fa-user-shield"></i>Notify cashiers that POS will be read-only.</li>
                    <li><i class="fas fa-history"></i>Document the time you started and finished.</li>
                </ul>
            </div>

            <div class="sync-card">
                <h3 class="text-lg font-semibold text-gray-900 mb-3">How the restore engine works</h3>
                <ol class="sync-process">
                    <li><span>1</span>Validate mime type, size, and encoding.</li>
                    <li><span>2</span>Disable foreign key checks + wrap DB::beginTransaction().</li>
                    <li><span>3</span>Stream each SQL statement to the database driver.</li>
                    <li><span>4</span>Commit and re-enable constraints, then purge the uploaded file.</li>
                    <li><span>5</span>Display success or detailed failure report with offending line.</li>
                </ol>
                <p class="text-xs text-gray-500 mt-3">Need a dry run first? Run <code>php artisan backup:restore /path/to/file.sql --dry</code> via CLI.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('syncForm', () => ({
        confirmed: false,
        fileName: '',
        error: '',
        dragActive: false,
        maxSize: 50 * 1024 * 1024,
        handleFile(event) {
            const file = event.target.files[0];
            this.processFile(file, event.target);
        },
        handleDrop(event) {
            const file = event.dataTransfer?.files?.[0];
            this.setDrag(false);
            if (!file) {
                return;
            }
            if (this.$refs.file) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.$refs.file.files = dataTransfer.files;
                this.processFile(file, this.$refs.file);
            } else {
                this.processFile(file);
            }
        },
        processFile(file, inputRef = null) {
            if (!file) {
                this.fileName = '';
                this.error = '';
                return;
            }

            if (file.size > this.maxSize) {
                this.error = 'File exceeds the 50MB limit. Please compress or remove data.';
                this.fileName = '';
                if (inputRef) {
                    inputRef.value = '';
                }
            } else {
                this.error = '';
                this.fileName = file.name;
            }
        },
        setDrag(state) {
            this.dragActive = state;
        },
        resetFile(event) {
            event.preventDefault();
            this.fileName = '';
            this.error = '';
            if (this.$refs.file) {
                this.$refs.file.value = '';
            }
        }
    }));
});
</script>
@endpush
@endsection
