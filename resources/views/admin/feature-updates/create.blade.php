@extends('layouts.central-admin')

@section('title', 'Create Feature Package')
@section('topbar-title', 'Create Feature Package')

@section('content')
<div class="ca-page-head" style="margin-bottom:24px;">
    <h1 class="ca-page-title">Create Feature Package</h1>
    <p class="ca-page-desc">Define a new feature package. The system will scaffold the selected files automatically.</p>
</div>

<div class="ca-card" style="max-width:800px;">
    <div class="ca-card-head" style="padding:14px 20px;">
        <span class="ca-card-title">Feature Configuration</span>
    </div>
    <div style="padding:20px;">
        @if($errors->any())
            <div style="background:#fdeaea;border:1px solid #e6c3c3;border-radius:8px;padding:12px 16px;margin-bottom:16px;">
                <ul style="margin:0;padding-left:18px;font-size:13px;color:#c0392b;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.feature-updates.store') }}" method="POST">
            @csrf

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                {{-- Name --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Feature Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Prayer Requests Module"
                           class="ca-input" style="width:100%;" required>
                </div>

                {{-- Slug --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" placeholder="e.g. prayer-requests"
                           class="ca-input" style="width:100%;" required>
                    <div style="font-size:11px;color:var(--ca-ink-4);margin-top:4px;">Lowercase, hyphenated. Used for table and class name generation.</div>
                </div>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
                {{-- Version --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Version *</label>
                    <input type="text" name="version" value="{{ old('version', '1.0.0') }}" placeholder="e.g. 1.0.0"
                           class="ca-input" style="width:100%;" required>
                    <div style="font-size:11px;color:var(--ca-ink-4);margin-top:4px;">Use format: major.minor.patch</div>
                </div>

                {{-- Type --}}
                <div>
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Type *</label>
                    <select name="type" class="ca-input" style="width:100%;" required>
                        <option value="feature" {{ old('type') === 'feature' ? 'selected' : '' }}>Feature (new functionality)</option>
                        <option value="patch" {{ old('type') === 'patch' ? 'selected' : '' }}>Patch (bug fix)</option>
                        <option value="data" {{ old('type') === 'data' ? 'selected' : '' }}>Data (data-only update)</option>
                    </select>
                </div>
            </div>

            {{-- Description --}}
            <div style="margin-bottom:32px;">
                <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Description</label>
                <textarea name="description" rows="4" placeholder="What this feature does — shown to church admins on the Updates page"
                          class="ca-input" style="width:100%;resize:vertical;">{{ old('description') }}</textarea>
            </div>

            <hr style="border:none;border-top:1px solid var(--ca-border);margin:32px 0;">

            {{-- Scaffold Options --}}
            <div style="margin-bottom:32px;">
                <h3 style="font-size:14px;font-weight:600;color:var(--ca-ink-1);margin-bottom:12px;">What to generate</h3>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[model]" value="1" checked> Model
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[migration]" value="1" checked> Migration
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[factory]" value="1" checked> Factory
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[seeder]" value="1" checked> Seeder (populate DB)
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[controller]" value="1" checked> Controller
                    </label>
                    <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--ca-ink-2);cursor:pointer;">
                        <input type="checkbox" name="scaffold[request]" value="1" checked> Form Request
                    </label>
                </div>
                
                <div style="max-width:200px;">
                    <label style="display:block;font-size:12px;font-weight:600;color:var(--ca-ink-3);margin-bottom:6px;">Seed rows per tenant</label>
                    <input type="number" name="seed_count" value="50" min="1" max="500" class="ca-input" style="width:100%;">
                </div>
            </div>

            <hr style="border:none;border-top:1px solid var(--ca-border);margin:32px 0;">

            {{-- Model Fields (Alpine.js) --}}
            <div x-data="fieldBuilder()" style="margin-bottom:32px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                    <h3 style="font-size:14px;font-weight:600;color:var(--ca-ink-1);margin:0;">Model fields</h3>
                    <button type="button" @click="addField()" class="ca-btn ca-btn-link" style="padding:0;">+ Add field</button>
                </div>
                
                <div style="border:1px solid var(--ca-border);border-radius:6px;overflow:hidden;margin-bottom:12px;">
                    <table style="width:100%;border-collapse:collapse;font-size:13px;">
                        <thead style="background:var(--ca-surface-dim);border-bottom:1px solid var(--ca-border);">
                            <tr>
                                <th style="padding:10px 12px;text-align:left;color:var(--ca-ink-3);font-weight:600;width:40%;">Field Name</th>
                                <th style="padding:10px 12px;text-align:left;color:var(--ca-ink-3);font-weight:600;width:35%;">Type</th>
                                <th style="padding:10px 12px;text-align:center;color:var(--ca-ink-3);font-weight:600;width:15%;">Nullable</th>
                                <th style="padding:10px 12px;text-align:right;width:10%;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(field, index) in fields" :key="field.id">
                                <tr style="border-bottom:1px solid var(--ca-border);">
                                    <td style="padding:10px 12px;">
                                        <input type="text" x-model="field.name" :name="'fields['+index+'][name]'" class="ca-input" style="width:100%;padding:6px 10px;" required>
                                    </td>
                                    <td style="padding:10px 12px;">
                                        <select x-model="field.type" :name="'fields['+index+'][type]'" class="ca-input" style="width:100%;padding:6px 10px;">
                                            <option value="string">string</option>
                                            <option value="text">text</option>
                                            <option value="integer">integer</option>
                                            <option value="boolean">boolean</option>
                                            <option value="date">date</option>
                                            <option value="foreignId">foreignId</option>
                                        </select>
                                    </td>
                                    <td style="padding:10px 12px;text-align:center;">
                                        <input type="checkbox" x-model="field.nullable" :name="'fields['+index+'][nullable]'" value="1">
                                    </td>
                                    <td style="padding:10px 12px;text-align:right;">
                                        <button type="button" @click="removeField(index)" class="ca-btn" style="padding:4px 8px;color:#c0392b;background:transparent;border:none;">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
                <div style="font-size:11px;color:var(--ca-ink-4);">Note: <code>id</code>, <code>user_id</code>, and <code>timestamps</code> are always included automatically.</div>
            </div>

            <div style="display:flex;gap:12px;align-items:center;">
                <button type="submit" class="ca-btn ca-btn-primary">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 002 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0022 16z"/></svg>
                    Create & scaffold
                </button>
                <span style="font-size:12px;color:var(--ca-ink-4);">Files will be generated in the background. Check back in a few seconds.</span>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const nameInput = document.querySelector('input[name="name"]');
        const slugInput = document.querySelector('input[name="slug"]');
        if (nameInput && slugInput) {
            nameInput.addEventListener('input', () => {
                slugInput.value = nameInput.value
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
            });
        }
    });

    function fieldBuilder() {
        return {
            fields: [
                { id: Date.now(), name: 'title', type: 'string', nullable: false },
                { id: Date.now() + 1, name: 'description', type: 'text', nullable: true }
            ],
            addField() {
                this.fields.push({
                    id: Date.now(),
                    name: '',
                    type: 'string',
                    nullable: false
                });
            },
            removeField(index) {
                this.fields.splice(index, 1);
            }
        }
    }
</script>
@endpush
@endsection
