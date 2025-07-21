<div class="row mb-3 align-items-center">
    <div class="col-md-10 d-flex align-items-center">
        <label class="me-2 fw-semibold">Search:</label>
        <input type="text" id="searchInput" class="form-control" placeholder="Search..." value="{{ $search ?? '' }}">
    </div>
    <div class="col-md-2 d-flex justify-content-end align-items-center">
        <label class="me-2 fw-semibold">Show</label>
        <select id="rowsPerPage" class="form-select w-auto">
            @foreach ([5, 10, 50, 100] as $value)
            <option value="{{ $value }}" {{ ($perPage == $value) ? 'selected' : '' }}>{{ $value }}</option>
            @endforeach
        </select>
        <label class="ms-2 fw-semibold">entries</label>
    </div>
</div>