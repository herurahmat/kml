<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css" integrity="sha512-P5MgMn1jBN01asBgU0z60Qk4QxiXo86+wlFahKrsQf37c9cro517WzVSPPV1tDKzhku2iJ2FVgL67wG03SGnNA==" crossorigin="anonymous" />
</head>

<body>
    <div class="row">
        <div class="col-4">
            <div class="card">
                <div class="card-header">Upload KML</div>
                <div class="card-body">
                    <form method="post" action="{{route('upload')}}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <input type="file" name="file" required />
                        </div>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="dump" id="chk">
                                <label class="form-check-label" for="chk"></label> Show DD
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-flat">Upload</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Search</div>
                <div class="card-body">
                    <form method="post" id="frmsearch">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="latitude" id="latitude" class="form-control " placeholder="Latitude" required value="-6.20061585" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="longitude" id="longitude" class="form-control " placeholder="Longitude" required value="106.7979651147864" />
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-flat">Search</button>
                        </div>
                    </form>
                    <div class="alert alert-info">
                        <h5>Result</h5>
                        <h1 id="lokasi"></h1>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-8">
            <table class="table table-bordered">
                <thead>
                    <th>Name</th>
                    <th>Poly</th>
                </thead>
                <tbody>
                    @foreach($data as $row)
                    <tr>
                        <td>{{ $row->name }}</td>
                        <td>{!! $row->poly_text !!}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {

            $("#frmsearch").on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                        url: "{{ route('calculate') }}",
                        data: $(this).serialize(),
                        type: "post",
                        dataType: "json",
                        beforeSend: function() {

                        },
                    })
                    .done(function(x) {
                        if (x.name != '') {
                            $("#lokasi").html(x.name);
                        }

                    })
                    .fail(function() {
                        alert('Server not respond');
                    })
                    .always(function() {

                    });
            });

        });
    </script>
</body>

</html>
