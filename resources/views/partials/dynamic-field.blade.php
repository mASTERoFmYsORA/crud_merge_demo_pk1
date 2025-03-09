<div class="input-group mb-2" id="field-{{ $index }}">
    <input type="text" name="custom[{{ $index }}][name]" class="form-control" placeholder="Enter Name" required>
    <input type="text" name="custom[{{ $index }}][value]" class="form-control" placeholder="Enter value" required>
    <button type="button" class="btn btn-danger remove-field" data-id="{{ $index }}">Remove</button>
</div>