<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css" integrity="sha384-oS3vJWv+0UjzBfQzYUhtDYW+Pj2yciDJxpsK1OYPAYjqT085Qq/1cq5FLXAZQ7Ay" crossorigin="anonymous">

    <title>Fam</title>

    <style>
        .eggs {
            display: flex;
            justify-content: space-between;
        }

        .eggs .btn-block {
            margin-top: 0;
        }

        .eggs i.fas {
            font-size: 2rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-4 offset-sm-4">

            <div class="card" style="margin-top: 2rem;">
                <div class="card-header" style="text-align: center; font-weight: 900; font-size: 3rem;">
                    FAM
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" autofocus placeholder="Name your Fam">
                        </div>
                        <div class="form-group eggs">
                            <button type="button" class="btn btn-light" data-id="1">
                                <i class="fas fa-fw fa-egg" style="color: #faf100;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="2">
                                <i class="fas fa-fw fa-egg" style="color: #ef5823;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="3">
                                <i class="fas fa-fw fa-egg" style="color: #622d93;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="4">
                                <i class="fas fa-fw fa-egg" style="color: #43a1b7;"></i>
                            </button>
                        </div>
                        <div class="form-group eggs">
                            <button type="button" class="btn btn-light" data-id="5">
                                <i class="fas fa-fw fa-egg" style="color: #f4ae16;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="6">
                                <i class="fas fa-fw fa-egg" style="color: #ed1b27;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="7">
                                <i class="fas fa-fw fa-egg" style="color: #273896;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="8">
                                <i class="fas fa-fw fa-egg" style="color: #3fa665;"></i>
                            </button>
                        </div>
                        <div class="form-group eggs">
                            <button type="button" class="btn btn-light" data-id="9">
                                <i class="fas fa-fw fa-egg" style="color: #f1801f;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="10">
                                <i class="fas fa-fw fa-egg" style="color: #bc0170;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="11">
                                <i class="fas fa-fw fa-egg" style="color: #316bb8;"></i>
                            </button>
                            <button type="button" class="btn btn-light" data-id="12">
                                <i class="fas fa-fw fa-egg" style="color: #a6d04e;"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="egg" />
                            <button type="submit" class="btn btn-primary btn-block" disabled>Choose an Egg</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>

<script>
    document.querySelectorAll(".eggs .btn").forEach(function (btn) {
        btn.addEventListener('click', function (e) {

            document.querySelectorAll(".eggs .btn").forEach(function (btn) {
                btn.classList.remove("active");
            });
            btn.classList.add("active");

            let submit = document.querySelector("button[type='submit']");
            submit.attributes.removeNamedItem("disabled");
            submit.innerHTML = "Hatch";

            document.querySelector("input[name='egg']").value = e.currentTarget.dataset.id;
        });
    });
</script>

</body>
</html>
