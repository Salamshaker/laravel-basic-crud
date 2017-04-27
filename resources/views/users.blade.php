<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Users and their jobs</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <style>
        .outer {
            display: table;
            position: absolute;
            height: 100%;
            width: 100%;
        }

        .middle {
            display: table-cell;
            vertical-align: middle;
        }

        .inner {
            margin-left: auto;
            margin-right: auto;
            width: 90%;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="outer">
        <div class="middle">
            <div class="inner">
                <div class="text-center">
                    <form class="form-inline" id="create-user">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input name="name" type="text" class="form-control" id="name">
                        </div>
                        <div class="form-group">
                            <label for="job">Job:</label>
                            <input name="job" type="text" class="form-control" id="job">
                        </div>
                        <button type="submit" class="btn btn-success">Add</button>
                    </form>
                </div>
                <table class="table table-bordered table-hover" id="mainTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Job</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->id}}</td>
                            <td>{{$user->name}}</td>
                            <td class="text-center">{{$user->job}}</td>
                            <td class="text-center">
                                <button class="btn btn-danger btn-xs btn-delete">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- JS HERE -->
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<script>
    var $table;

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": false,
            "progressBar": true,
            "positionClass": "toast-top-full-width",
            "preventDuplicates": true,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        $table = $('#mainTable').DataTable({
            "order": [[1, "asc"]],
            "lengthMenu": [[10, 15, 25, 50, -1], [10, 15, 25, 50, "All"]],
            "pageLength": 15
        });
    });

    $("#create-user").submit(function (e) {
        e.preventDefault();
        var $data = $(this).serializeArray().reduce(function (m, o) {
            m[o.name] = o.value;
            return m;
        }, {});

        if ($data.name != "" && $data.job != "") {
            $.ajax({
                type: "POST",
                data: JSON.stringify($data),
                contentType: "application/json; charset=utf-8",
                success: function ($response) {
                    var $node = $table.row.add([
                        $response.id,
                        $response.name,
                        $response.job,
                        '<button class="btn btn-danger btn-xs btn-delete">Delete</button>'
                    ]).draw().node();

                    $($node).find('td').eq(2).addClass('text-center');
                    $($node).find('td').eq(3).addClass('text-center');
                    toastr["success"]($response.name + " was added successfully");
                },
                error: function () {
                    toastr["error"]("Something went wrong adding, please try again.");
                }
            });
        } else {
            toastr["warning"]("You are missing either Name or Job, please try again.");
        }
    });

    $('#mainTable').on('click', 'tbody td', function () {
        var $cell = $table.cell(this);
        var $data = getRowData($table
            .row($(this).closest('tr'))
            .data()
        );

        $promptColumn = null;
        switch ($(this).parent().children().index($(this))) {
            case 1:
                $promptColumn = 'name';
                break;
            case 2:
                $promptColumn = 'job';
                break;
        }

        if ($promptColumn != null) {
            var $prompt = prompt("Please enter new " + $promptColumn + ":", $data[$promptColumn]);
            if ($prompt != null && $prompt != "") {

                //UPDATE OBJECT WITH NEW DATA
                $data[$promptColumn] = $prompt;

                $.ajax({
                    type: "PUT",
                    url: '{{Request::url().'/'}}' + $data.id,
                    data: JSON.stringify($data),
                    contentType: "application/json; charset=utf-8",
                    success: function () {
                        toastr["success"]("Updated successfully.");
                        $cell.data($prompt).draw();
                    },
                    error: function () {
                        toastr["error"]("Something went wrong updating, please try again.");
                    }
                });
            } else {
                toastr["warning"]("The entered information was empty, please try again");
            }
        }
    });

    $('#mainTable tbody').on('click', '.btn-delete', function () {
        var $btn = $(this);
        var $data = getRowData($table
            .row($btn.closest('tr'))
            .data()
        );

        $.ajax({
            type: "DELETE",
            url: '{{Request::url().'/'}}' + $data.id,
            success: function () {
                $table
                    .row($btn.closest('tr'))
                    .remove()
                    .draw();
                toastr["success"]("Succesfully deleted the user.");
            },
            error: function () {
                toastr["error"]("Something went wrong removing the user, please try again.");
            }
        });
    });


    function getRowData($row) {
        return {
            "id": $row[0],
            "name": $row[1],
            "job": $row[2]
        };
    }
</script>
</body>
</html>
