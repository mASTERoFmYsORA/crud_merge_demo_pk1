@extends('layouts.main')

@section('content')
    <div class="card mt-5">
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            <h2>Contacts List</h2>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a class="btn btn-success btn-sm" href="{{ route('contacts.create') }}">Create
                    Contact</a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped display wrap" id="contacts-table">
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th width="80px">No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Profile Image</th>
                            <th>Additional file</th>
                            <th>Merged Email</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <!-- <tbody>
                                                                                @foreach ($contacts as $key => $contact)
                                                                                    <tr>
                                                                                        <td>{{ $key + 1 }}</td>
                                                                                        <td>{{ $contact->name }} </td>
                                                                                        <td>{{ $contact->email }}</td>
                                                                                        <td>{{ $contact->phone }}</td>
                                                                                        <td>{{ $contact->gender }}</td>
                                                                                        @if ($contact->profile_image)
                                                                                            <td><img src="{{ asset('storage/' . $contact->profile_image) }}" width="100"></td>
                                                                                        @else
                                                                                            <td></td>
                                                                                        @endif
                                                                                        @if ($contact->additional_file)
                                                                                            <td><a href="{{ asset('storage/' . $contact->additional_file) }}" download>Download</a>
                                                                                            </td>
                                                                                        @else
                                                                                            <td></td>
                                                                                        @endif
                                                                                        <td>
                                                                                            <form action="{{ route('contacts.destroy', $contact->id) }}" method="POST">
                                                                                                <a class="btn btn-primary btn-sm" href="{{ route('contacts.edit', $contact->id) }}">Edit</a>
                                                                                                @csrf
                                                                                                @method('DELETE')
                                                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                                                    Delete</button>
                                                                                            </form>
                                                                                        </td>
                                                                                    </tr>
                                                                                @endforeach
                                                                            </tbody> -->
                </table>
            </div>
        </div>
    </div>
    <!-- <div>
            <div id="mergeModal" style="display: none;">
                <h2>Select Master Contact</h2>
                <div id="mergeOptions"></div>
                <button id="confirmMerge">Confirm Merge</button>
            </div>
        </div> -->
    <div id="mergeModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merge Contacts</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Select a master contact:</p>
                    <div id="mergeOptions"></div> <!-- Contact selection area -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="confirmMerge">Merge</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#contacts-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('contacts.get-index-data') }}',
                columns: [
                    {
                        data: 'id', render: function (data, type, row) {
                            return `<input type="checkbox" class="select-contact" value="` + data + `">`;
                        }
                    },
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'email', name: 'email' },
                    { data: 'phone', name: 'phone' },
                    { data: 'gender', name: 'gender' },
                    {
                        data: 'profile_image', name: 'profile_image', render: function (data) {
                            return data ? '<img src="/storage/' + data + '" height="100" width="100"/>' : '';
                        }
                    },
                    {
                        data: 'additional_file', name: 'additional_file', render: function (data) {
                            return data ? '<a href="/storage/' + data + '" target="_blank">Download</a>' : 'No File';
                        }
                    },
                    { data: 'merged_email', name: 'merged_email' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });
        });
        $(document).ready(function () {
            var table = $('#contacts-table').DataTable();

            // Delete record
            $('#contacts-table').on('click', '.deleteRecord', function () {
                var id = $(this).data('id');
                var url = $(this).data('url');
                var token = $('meta[name="csrf-token"]').attr('content');

                if (confirm("Are you sure you want to delete this record?")) {
                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        data: { _token: "{{ csrf_token() }}" }, // Laravel CSRF Token
                        success: function (response) {
                            table.ajax.reload();
                            alert(response.success);
                        },
                        error: function (xhr) {
                            alert('An error occurred while deleting the record.');
                        }
                    });
                }
            });
        });

        let selectedContacts = [];

        $(document).on('change', '.select-contact', function () {
            let id = $(this).val();
            if ($(this).is(':checked')) {
                selectedContacts.push(id);
            } else {
                selectedContacts = selectedContacts.filter(c => c !== id);
            }
        });

        $(document).on('click', '.merge-btn', function () {
            if (selectedContacts.length !== 2) {
                alert("Please select exactly 2 contacts to merge.");
                return;
            }
            $('#mergeOptions').html('');
            selectedContacts.forEach(id => {
                $('#mergeOptions').append(`<input type="radio" name="masterContact" value="${id}"> Contact ID: ${id} <br>`);
            });

            $('#mergeModal').modal('show');
            console.log('sdfsdf');
        });

        // Confirm merge
        $('#confirmMerge').on('click', function () {
            let masterId = $('input[name="masterContact"]:checked').val();
            let secondaryId = selectedContacts.find(id => id !== masterId);

            if (!masterId || !secondaryId) {
                alert("Please select a master contact.");
                return;
            }
            var table = $('#contacts-table').DataTable();
            $.ajax({
                url: "{{ route('contacts.merge-data') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    master_id: masterId,
                    secondary_id: secondaryId
                },
                success: function (response) {
                    alert(response.success);
                    table.ajax.reload();
                    $('#mergeModal').modal('hide');
                    selectedContacts = [];
                    table.ajax.reload();
                },
                error: function (response) {
                    alert("Error: " + response.responseJSON.error);
                }
            });
        });
    </script>
@endsection