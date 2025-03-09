@extends('layouts.main')

@section('content')
    <div class="card mt-5">
        <h2 class="card-header">Add New Contact</h2>
        <div class="card-body">

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-primary btn-sm" href="{{ route('contacts.index') }}">
                    Back</a>
            </div>

            <form action="{{ route('contacts.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <label for="inputName" class="form-label"><strong>Name:</strong></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name">
                @error('name')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputEmail" class="form-label"><strong>Email:</strong></label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email">
                @error('email')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputPhone" class="form-label"><strong>Phone:</strong></label>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror"
                    placeholder="Phone" maxlength="10" minlength="10">
                @error('phone')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputGender" class="form-label"><strong>Gender:</strong></label>
                <input type="radio" name="gender" value="Male"> Male
                <input type="radio" name="gender" value="Female"> Female
                <input type="radio" name="gender" value="Other"> Other

                @error('gender')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
                <br>
                <label for="inputProfileImage" class="form-label"><strong>Profile Image:</strong></label>
                <input type="file" class="form-control" name="profile_image" @error('profile_image') is-invalid @enderror>

                @error('profile_image')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror

                <label for="inputAdditionalFile" class="form-label"> <strong>Additional File:</strong></label>
                <input type="file" class="form-control" name="additional_file" @error('additional_file') is-invalid
                @enderror><br>

                @error('additional_file')
                    <div class="form-text text-danger">{{ $message }}</div>
                @enderror
                <h3>Custom Fields</h3>
                <button type="button" class="btn btn-primary mt-3" id="add-field">Add Field</button>
                <div id="custom-fields"></div>

                
                <button type="submit" class="btn btn-success mt-3">Submit</button>
            </form>

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