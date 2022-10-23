<template>
<template-component>
   <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3>150</h3>

          <p>Sendika Davaları</p>
        </div>
        <div class="icon">
          <i class="ion ion-bag"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>53<sup style="font-size: 20px">%</sup></h3>

          <p>Bireysel Davalar</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <!-- ./col -->
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-success">
        <div class="inner">
          <h3>65</h3>

          <p>Toplam Davalar</p>
        </div>
        <div class="icon">
          <i class="ion ion-pie-graph"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row -->
  
  <div class="row">
    <div class="col-5">
      <canvas id="myChart1"></canvas>
    </div>
    <div class="col-1"></div>
    <div class="col-5">
      <canvas id="myChart2"></canvas>
    </div>
  </div>
  
  <div class="row mt-5">
    <div class="col-5">
      <canvas id="myChart3"></canvas>
    </div>
    <div class="col-1"></div>
    <div class="col-5">
      <canvas id="myChart4"></canvas>
    </div>
  </div>

  <hr/>

  <div class="row">
    <div class="col-12">
      <table class="table table-striped table-inverse table-responsive table-bordered">
        <thead class="thead-inverse">
          <tr>
            <th>Konusu</th>
            <th>Bireysel</th>
            <th>İlksen</th>
            <th>Birsen</th>
            <th>Türksen</th>
            <th>İşsen</th>
            <th>Asen</th>
            <th>Bsen</th>
            <th>Csen</th>
            <th>Dsen</th>
            <th>Esen</th>
            <th>TOPLAM</th>
          </tr>
          </thead>
          <tbody>
            <tr>
              <td scope="row">Anayasaya açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">13. Maddeye açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">Anayasaya açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">13. Maddeye açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">Anayasaya açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">13. Maddeye açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">Anayasaya açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">13. Maddeye açıldı</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>20</td>
            </tr>
            <tr>
              <td scope="row">TOPLAM</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>12</td>
              <td>3</td>
              <td>5</td>
              <td>0</td>
              <td>2</td>
              <td>200</td>
            </tr>
          </tbody>
      </table>
    </div>
  </div>

  <div class="row">
    <div class="show-stat">
      <b>Bireysel Davalar </b>
    </div>
    <div>
      <span>: 102</span>
    </div>
  </div>

  <div class="row mt-1">
    <div class="show-stat">
      <b>Sendika Davalar </b>
    </div>
    <div>
      <span>: 220</span>
    </div>
  </div>

  <div class="row mt-1">
    <div class="show-stat">
      <b>Toplam Davalar </b>
    </div>
    <div>
      <span>: 322</span>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-1">
      <button type="button" class="btn btn-block btn-primary btn-flat">
        PDF Çıktısı
      </button>
    </div>
  </div>

</template-component>
</template>

<script>
import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(ChartDataLabels, ...registerables);
// Chart.register(ChartDataLabels);

export default {
  name: 'createComponent',
  data () {
    return {
      
    };
  },
  mounted() {
    const ctx1 = document.getElementById('myChart1').getContext('2d');
    const ctx2 = document.getElementById('myChart2').getContext('2d');
    const ctx3 = document.getElementById('myChart3').getContext('2d');
    const ctx4 = document.getElementById('myChart4').getContext('2d');
    
    const data = {
      /* labels: [
        'My First DatasetMy First DatasetMy First DatasetMy First Dataset', 
        'bMy First DatasetMy First DatasetMy First DatasetMy First Dataset', 
        'cMy First DatasetMy First DatasetMy First Dataset', 
        'dMy First DatasetMy First DatasetMy First DatasetMy First DatasetMy First Dataset', 
        'eMy First DatasetMy First DatasetMy First DatasetMy First Dataset', 
        'fMy First DatasetMy First DatasetMy First Dataset', 
        'gMy First DatasetMy First Dataset'
      ], */
      labels: ['January', 'February', 'March', 'April', 'May', 'June'],
      datasets: [{
        axis: 'y',
        label: 'My First Dataset',
        data: [65, 59, 80, 81, 56, 55, 40],
        backgroundColor: [
          'rgba(255, 99, 132, 0.2)',
          'rgba(255, 159, 64, 0.2)',
          'rgba(255, 205, 86, 0.2)',
          'rgba(75, 192, 192, 0.2)',
          'rgba(54, 162, 235, 0.2)',
          'rgba(153, 102, 255, 0.2)',
          'rgba(201, 203, 207, 0.2)'
        ],
        borderColor: [
          'rgb(255, 99, 132)',
          'rgb(255, 159, 64)',
          'rgb(255, 205, 86)',
          'rgb(75, 192, 192)',
          'rgb(54, 162, 235)',
          'rgb(153, 102, 255)',
          'rgb(201, 203, 207)'
        ],
        borderWidth: 1,
        datalabels: {
          align: 'end',
          anchor: 'end',
          /* listeners: {
            click: function(context, event) {
              // Receives `click` events only for labels of the first dataset.
              // The clicked label index is available in `context.dataIndex`.
              console.log(context);
              console.log('label ' + context.dataIndex + ' has been clicked!');
              console.log('mouse is at position x:', event.x, 'and y:', event.y);

              if (event.native.ctrlKey) {
                console.log('control key is pressed!');
              }
            }
          } */
        }
      }]
    };

    const config = {
      type: 'bar',
      data: data,
      options: {
        indexAxis: 'y',
        elements: {
          bar: {
            borderWidth: 2,
          }
        },
        responsive: true,

        scales: {
          y: {
            barPercentage: 0.1
          }
        },
        
        plugins: {
          /* legend: {
            position: 'bottom',
          }, */
          title: {
            display: true,
            text: 'Sendika Konularına Göre Dava Sayısı'
          }
        },
      },
    };

    const myChart1 = new Chart(ctx1, config);
    const myChart2 = new Chart(ctx2, config);
    const myChart3 = new Chart(ctx3, config);
    const myChart4 = new Chart(ctx4, config);

  }
}
</script>

<style>
.show-stat {
  width: 125px;
  margin-left: 10px;
}
</style>