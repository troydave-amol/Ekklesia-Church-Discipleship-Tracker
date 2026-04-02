{{-- Inline tab: same fields as full form, posts to users.store --}}
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="grid-2" style="gap:16px;">
                <div class="form-group">
                    <label class="form-label">First name</label>
                    <input name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Last name</label>
                    <input name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control">
                        <option value="disciple" {{ old('role','disciple')==='disciple'?'selected':'' }}>Disciple</option>
                        <option value="program_leader" {{ old('role')==='program_leader'?'selected':'' }}>Program leader</option>
                        <option value="cell_leader" {{ old('role')==='cell_leader'?'selected':'' }}>Cell leader</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assigned leader</label>
                    <select name="leader_id" class="form-control">
                        <option value="">—</option>
                        @foreach($leaders ?? [] as $l)
                            <option value="{{ $l->id }}" {{ (string)old('leader_id')===(string)$l->id?'selected':'' }}>{{ $l->first_name }} {{ $l->last_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        @foreach(['active','inactive','visitor','new_member'] as $opt)
                            <option value="{{ $opt }}" {{ old('status','new_member')===$opt?'selected':'' }}>{{ ucfirst(str_replace('_',' ', $opt)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Training level</label>
                    <select name="destiny_training_level" class="form-control">
                        @foreach(['life_class','sol1','sol2','sol3','graduate'] as $opt)
                            <option value="{{ $opt }}" {{ old('destiny_training_level','life_class')===$opt?'selected':'' }}>{{ strtoupper(str_replace('_',' ', $opt)) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mt-16">Add member</button>
        </form>
    </div>
</div>
