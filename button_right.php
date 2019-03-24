<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Document</title>
    <link rel="stylesheet" href="css/bootstrap.css">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
</head>

<body>

    <div class="container">

        <div class="row">
            <div class="col-md-4">
                <div class="input-group">
                    <input class="form-control py-2 border-right-0 border" type="search" value="search" id="example-search-input">
                    <span class="input-group-append">
                        <button class="btn btn-outline-secondary border-left-0 border" type="button">
                            <i class="fa fa-search"></i>
                        </button>
                    </span>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <select class="form-control" id="exampleFormControlSelect1">
                        <option>1</option>
                        <option>2</option>
                        <option>3</option>
                        <option>4</option>
                        <option>5</option>
                    </select>
                    
                    <?php
                    
                    $date = date('YmdHis');
                    
                    echo "$date";
                    
                    ?>
                </div>
            </div>

        </div>

        <div class="row">
           <div class="col col-md-8">
               <div class="container">
                <table class="table table-striped">
                    <thead>
                        <th>Blotter Entry Number</th>
                        <th>Offense</th>
                        <th>Date Committed</th>
                        <th>Status</th>
                        <th>Investigator on Case</th>
                        <th>Remarks</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Lorem ipsum dolor sit amet.</td>
                            <td>Ea accusamus quod qui, reprehenderit.</td>
                            <td>Aspernatur eos voluptas perspiciatis dolor.</td>
                            <td>Eum praesentium adipisci quo illo.</td>
                            <td>Quam expedita officiis ut et?</td>
                            <td>Recusandae dolorem iste quos consectetur!</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit amet.</td>
                            <td>Quia rerum molestias odio earum!</td>
                            <td>Eius voluptatum eum, mollitia magni!</td>
                            <td>Itaque minima hic nisi ea.</td>
                            <td>Veniam ratione, mollitia error est?</td>
                            <td>Facilis, modi nesciunt enim vitae?</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit amet.</td>
                            <td>Ratione sunt nesciunt, voluptate praesentium.</td>
                            <td>Nihil pariatur, necessitatibus qui nisi.</td>
                            <td>Quidem libero cupiditate porro repudiandae?</td>
                            <td>Numquam officia expedita facere omnis!</td>
                            <td>Consequuntur facilis ipsam consequatur, voluptatum.</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit amet.</td>
                            <td>Maiores, sit accusamus itaque vitae.</td>
                            <td>Illum nulla voluptas itaque optio.</td>
                            <td>Consequatur atque molestiae labore excepturi!</td>
                            <td>Dolorem, modi. Ullam, consectetur. Excepturi.</td>
                            <td>Aliquid ab quod enim consectetur!</td>
                        </tr>
                        <tr>
                            <td>Lorem ipsum dolor sit amet.</td>
                            <td>Fuga accusamus illum dolores, magnam!</td>
                            <td>Debitis hic quidem consequatur atque?</td>
                            <td>Quidem aperiam voluptatibus, ipsam blanditiis.</td>
                            <td>Itaque tempora vitae architecto a?</td>
                            <td>Fuga iste eum, eveniet architecto?</td>
                        </tr>
                    </tbody>
                </table>
            </div>
           </div>
           
           <div class="col col-md-4">
               <button class="btn btn-primary">Button</button>
           </div>
            
        </div>

    </div>

    <script src="js/jquery-3.3.1.slim.min.js"></script>
    <script src="js/bootstrap.bundle.min.js"></script>

</body>

</html>
