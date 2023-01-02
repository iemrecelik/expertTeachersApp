<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    
    <style>
        *{
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        body{
            font-family: Helvetica;
            -webkit-font-smoothing: antialiased;
            /* background: rgba( 71, 147, 227, 1); */
        }
        h2{
            text-align: center;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white;
            padding: 30px 0;
        }

        /* Table Styles */

        .table-wrapper{
            margin: 10px 70px 70px;
            /* box-shadow: 0px 35px 50px rgba( 0, 0, 0, 0.2 ); */
        }

        .fl-table {
            border-radius: 5px;
            font-size: 12px;
            font-weight: normal;
            border: none;
            border-collapse: collapse;
            width: 100%;
            max-width: 100%;
            white-space: nowrap;
            background-color: white;
        }

        .fl-table td, .fl-table th {
            text-align: center;
            padding: 8px;
        }

        .fl-table td {
            border-right: 1px solid #f8f8f8;
            font-size: 12px;
        }

        .fl-table thead th {
            color: #ffffff;
            background: #4FC3A1;
        }


        .fl-table thead th:nth-child(odd) {
            color: #ffffff;
            background: #324960;
        }

        .fl-table tr:nth-child(even) {
            background: #F8F8F8;
        }

        /* Responsive */

        @media (max-width: 767px) {
            .fl-table {
                display: block;
                width: 100%;
            }
            .table-wrapper:before{
                content: "Scroll horizontally >";
                display: block;
                text-align: right;
                font-size: 11px;
                color: white;
                padding: 0 0 10px;
            }
            .fl-table thead, .fl-table tbody, .fl-table thead th {
                display: block;
            }
            .fl-table thead th:last-child{
                border-bottom: none;
            }
            .fl-table thead {
                float: left;
            }
            .fl-table tbody {
                width: auto;
                position: relative;
                overflow-x: auto;
            }
            .fl-table td, .fl-table th {
                padding: 20px .625em .625em .625em;
                height: 60px;
                vertical-align: middle;
                box-sizing: border-box;
                overflow-x: hidden;
                overflow-y: auto;
                width: 120px;
                font-size: 13px;
                text-overflow: ellipsis;
            }
            .fl-table thead th {
                text-align: left;
                border-bottom: 1px solid #f7f7f9;
            }
            .fl-table tbody tr {
                display: table-cell;
            }
            .fl-table tbody tr:nth-child(odd) {
                background: none;
            }
            .fl-table tr:nth-child(even) {
                background: transparent;
            }
            .fl-table tr td:nth-child(odd) {
                background: #F8F8F8;
                border-right: 1px solid #E6E4E4;
            }
            .fl-table tr td:nth-child(even) {
                border-right: 1px solid #E6E4E4;
            }
            .fl-table tbody td {
                display: block;
                text-align: center;
            }
        }
        .show-stat {
            float: left;
            width: 155px;
        }
        .show-stat-total {
            float: left;
        }
        .row-total-stats {
            display: table;
        }
        .col-total-stats {
            display: table;
            margin-top: 11px;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-12 table-wrapper">
          <table class="fl-table table table-striped table-inverse table-responsive table-bordered">
            <thead class="thead-inverse">
              <tr>
                <th>Konusu</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>Bireysel</th>
                <th>TOPLAM</th>
              </tr>
              </thead>
              <tbody>
                <tr v-for="tblStItem in stats.tableStats">
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                </tr>
                <tr>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                    <td>klfd</td>
                </tr>
              </tbody>
          </table>

          <div class="row-total-stats row">
            <div class="col-total-stats col-3">
              <div class="show-stat float-left">
                <b>Bireysel Davalar </b>
              </div>
              <div class="show-stat-total float-left">
                <span>1212</span>
              </div>
            </div>
          </div>
      
          <div class="row-total-stats row mt-1">
            <div class="col-total-stats col-3">
              <div class="show-stat float-left">
                <b>Sendika Davalar </b>
              </div>
              <div class="show-stat-total float-left">
                <span>214124</span>
              </div>
            </div>
          </div>
      
          <div class="row-total-stats row mt-1">
            <div class="col-total-stats col-3">
              <div class="show-stat float-left">
                <b>Toplam Davalar </b>
              </div>
              <div class="show-stat-total float-left">
                <span>124124</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      
</body>
</html>