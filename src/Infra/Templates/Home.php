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
        .actions {
            display: flex;
            justify-content: space-around;
            margin-top: 2rem;
            text-align: center;
        }

        .actions i.fas {
            font-size: 1.6rem;
        }

        .fam-happy {
            animation: happy 1s cubic-bezier(0.75,0,0.75,0.9) 3;
        }

        .fam-distressed {
            margin-left: -<?=$fam->distress * 2?>rem;
            animation: distressed 1s infinite;
        }

        .fam-sick {
            animation: sick 1s 3;
        }

        @keyframes happy {
            50% {
                transform: translateY(-1rem);
            }
        }

        @keyframes distressed {
            50% {
                transform: translateX(<?=$fam->distress * 4?>rem);
            }
        }

        @keyframes sick {
            50% {
                transform: rotate(45deg);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-sm-4 offset-sm-4">

            <div class="card" style="margin-top: 2rem;">
                <div class="card-header" style="text-align: center;">
                    <?=$fam->name?>
                </div>
                <div class="card-body">
                    <div class="fam
                                <?=$fam->isHappy ? "fam-happy" : ""?>
                                <?=$fam->distress > 0 ? "fam-distressed" : ""?>
                                <?=$fam->isSick ? "fam-sick" : ""?>
                                "
                         style="text-align: center; font-size: 10rem; line-height: 1;"
                    >
                        <?php if ($fam->isAlive) : ?>
                            <i class="fas fa-fw fa-<?=$fam->speciesIcon?>"></i>
                        <?php else : ?>
                            <i class="fas fa-fw fa-skull-crossbones"></i>
                        <?php endif ?>
                    </div>
                    <div class="actions">
                        <form action="/<?=$fam->id?>/feed" method="POST">
                            <button type="submit" class="btn btn-light" <?=$fam->isAlive ? "" : "disabled"?>>
                                <i class="fas fa-fw fa-hamburger"></i>
                            </button>
                        </form>
                        <form action="/<?=$fam->id?>/play" method="POST">
                            <button type="submit" class="btn btn-light" <?=$fam->isAlive ? "" : "disabled"?>>
                                <i class="fas fa-fw fa-baseball-ball"></i>
                            </button>
                        </form>
                        <form action="/post" method="POST">
                            <button type="button" class="btn btn-light" disabled><i class="fas fa-fw fa-mobile-alt"></i></button>
                        </form>
                    </div>
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
</body>
</html>
