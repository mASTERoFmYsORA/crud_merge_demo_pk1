@extends('layouts.main')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Edit Contact</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('contacts.index') }}">
                    Back</a>
            </div>

            <form action="{{ route('contacts.update', $contact->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="inputName" class="form-label"><strong>Name:</strong></label>
                <input type="text" name="name" value="{{ $contact->name }}"
                    class="form-control @error('name') is-invalid @enderror" placeholder="Name">
                @error('name')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputEmail" class="form-label"><strong>Email:</strong></label>
                <input type="email" name="email" value="{{ $contact->email }}"
                    class="form-control @error('email') is-invalid @enderror" placeholder="Email">
                @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputPhone" class="form-label"><strong>Phone:</strong></label>
                <input type="tel" name="phone" value="{{ $contact->phone }}"
                    class="form-control @error('phone') is-invalid @enderror" placeholder="Phone" maxlength="10" minlength="10">
                @error('phone')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputGender" class="form-label"><strong>Gender:</strong></label><br>
                <input type="radio" name="gender" value="Male" {{ $contact->gender == 'Male' ? 'checked' : ''}}> Male
                <input type="radio" name="gender" value="Female" {{ $contact->gender == 'Female' ? 'checked' : '' }}> Female
                <input type="radio" name="gender" value="Other" {{ $contact->gender == 'Other' ? 'checked' : '' }}> Other

                @error('gender')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
                <br>

                <label for="inputProfileImage" class="form-label"><strong>Profile Image:</strong></label>
                <input type="file" class="form-control" name="profile_image" @error('profile_image') is-invalid @enderror>
                @if ($contact->profile_image)
                    <img src="{{ asset('storage/' . $contact->profile_image) }}" width="100">
                @endif

                @error('profile_image')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
                <br>
                <label for="inputAdditionalFile" class="form-label"> <strong>Additional File:</strong></label>
                <input type="file" class="form-control" name="additional_file" @error('additional_file') is-invalid
                @enderror><br>

                @if ($contact->additional_file)
                    <a href="{{ asset('storage/' . $contact->additional_file) }}" download>Download</a>

                @endif

                @error('additional_file')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
                <h3>Custom Fields</h3>
                <button type="button" class="btn btn-primary mt-3" id="add-field">Add Field</button>
                <div id="custom-fields"></div>
                
                @if(count($contact->customFields) > 0)
                    @foreach ($contact->customFields as $key=>$custom)
                        <div class="input-group mb-2" id="field-{{ $key }}">
                            <input type="text" name="custom[{{ $key }}][name]" class="form-control" placeholder="Enter Name" value="{{ $custom->field_name }}">
                            <input type="text" name="custom[{{ $key }}][value]" class="form-control" placeholder="Enter value" value="{{ $custom->field_value }}">
                            <button type="button" class="btn btn-danger remove-field" data-id="{{ $key }}">Remove</button>
                        </div>
                    @endforeach
                @endif
                
                <button type="submit" class="btn btn-success mt-3"> Save</button>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            let fieldIndex = 1;

            // Add new field
            $('#add-field').click(function () {
                $.ajax({
                    url: '{{ route("add-field") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        index: fieldIndex
                    },
                    success: function (response) {
                        $('#custom-fields').append(response.html);
                        fieldIndex++;
                    }
                });
            });

            // Remove field
            $(document).on('click', '.remove-field', function () {
                let fieldId = $(this).data('id');
                $('#field-' + fieldId).remove();
            });
        });
    </script>
@endsection