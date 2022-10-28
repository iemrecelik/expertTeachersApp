<template>
<template-component>
   <!-- Small boxes (Stat box) -->
  <div class="row">
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-info">
        <div class="inner">
          <h3>{{ stats.unsSum }}</h3>

          <p>Sendika Davaları</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
      <!-- small box -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h3>{{ stats.thrSum }}<!-- <sup style="font-size: 20px">%</sup> --></h3>

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
          <h3>{{ (stats.unsSum + stats.thrSum) }}</h3>

          <p>Toplam Davalar</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <!-- <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
      </div>
    </div>
    <!-- ./col -->
  </div>
  <!-- /.row -->
  
  <div class="row">
    <div class="col-5">
      <canvas id="stats1"></canvas>
    </div>
    <div class="col-1"></div>
    <div class="col-5">
      <canvas id="stats2"></canvas>
    </div>
  </div>
  
  <div class="row mt-5">
    <div class="col-5">
      <canvas id="stats3"></canvas>
    </div>
    <div class="col-1"></div>
    <div class="col-5">
      <canvas id="stats4"></canvas>
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
            <th v-for="item in stats.unsNames">{{item}}</th>
            <th>TOPLAM</th>
          </tr>
          </thead>
          <tbody>
            <tr v-for="tblStItem in stats.tableStats">
              <td scope="row">{{tblStItem.law_brief}}</td>
              <td>{{writeBriefStat(tblStItem.law_brief, stats.thrBriefCount)}}</td>
              <td v-for="unsStNameItem in stats.unsNames">{{writeUnsStat(unsStNameItem, tblStItem.unsCount)}}</td>
              <td>{{writeBriefStat(tblStItem.law_brief, stats.sumBriefCount)}}</td>
            </tr>
            <tr>
              <td scope="row">TOPLAM</td>
              <td>{{stats.thrSum}}</td>
              <td v-for="unsName in stats.unsNames">{{writeUnsStat(unsName, stats.unsCount)}}</td>
              <td>{{ (stats.unsSum + stats.thrSum) }}</td>
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
      <span>: {{ stats.thrSum }}</span>
    </div>
  </div>

  <div class="row mt-1">
    <div class="show-stat">
      <b>Sendika Davalar </b>
    </div>
    <div>
      <span>: {{ stats.unsSum }}</span>
    </div>
  </div>

  <div class="row mt-1">
    <div class="show-stat">
      <b>Toplam Davalar </b>
    </div>
    <div>
      <span>: {{ (stats.unsSum + stats.thrSum) }}</span>
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

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'IndexComponent',
  data () {
    return {
      stats: this.ppstats,
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
    };
  },
  props: {
    pproutes: {
      type: Object,
      required: true,
    },
    pperrors: {
      type: Object,
      required: true,
    },
    ppstats: {
      type: Object,
      required: false,
    },
  },
  computed: {
    ...mapState([
      'routes',
      'errors',
    ]),
  },
  methods: {
    ...mapMutations([
      'setRoutes',
      'setErrors'
    ]),
    writeUnsStat(unsName, statsItem) {
      let count = 0;
      statsItem.forEach(item => {
        if(unsName == item.uns_name) {
          count = item.count;
          return false;
        }
      });

      return count;
    },
    writeBriefStat(brief, statsItem) {
      let count = 0;
      statsItem.forEach(item => {
        if(brief == item.law_brief) {
          count = item.count;
          return false;
        }
      });

      return count;
    },
    strToArray(str, limit) {
      const words = str.split(' ')
      let aux = []
      let concat = []

      for (let i = 0; i < words.length; i++) {
          concat.push(words[i])
          let join = concat.join(' ')
          if (join.length > limit) {
              aux.push(join)
              concat = []
          }
      }

      if (concat.length) {
          aux.push(concat.join(' ').trim())
      }

      return aux
    },
    createStats() {

      let labels  = [];
      let datas = [];
      let co  = 0;
      let backgroundColor = [];
      let borderColor = [];
      let sum = 0;

      let data = {};
      let config = {};

      for (let i = 1; i < 5; i++) {
        labels  = [];
        datas = [];
        co  = 0;
        backgroundColor = [];
        borderColor = [];
        sum = 0;

        switch (i) {
          case 1:
            const ctx1 = document.getElementById('stats'+i).getContext('2d');

            this.stats.sumBriefCount.forEach(item => {
              backgroundColor.push(this.backgroundColor[co]);
              borderColor.push(this.borderColor[co]);
              co++;
              labels.push(this.strToArray(item.law_brief, 30));
              datas.push(item.count);

              sum = sum > item.count ? item.count : sum;
            });

            data = {
              labels,
              datasets: [{
                axis: 'y',
                label: 'Dava Sayısı',
                data: datas,
                backgroundColor,
                borderColor,
                borderWidth: 1,
                datalabels: {
                  align: 'end',
                  anchor: 'end',
                }
              }]
            };

            config = {
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
                  },
                  x: {
                    suggestedMin: 0,
                    suggestedMax: (sum+10)
                  }
                },
                
                plugins: {
                  legend: {
                    display: false
                  },
                  title: {
                    display: true,
                    text: 'Toplam Konularına Göre Dava Sayısı'
                  }
                },
              },
            };

            const myChart1 = new Chart(ctx1, config);
            break;
          case 2:
            const ctx2 = document.getElementById('stats'+i).getContext('2d');

            this.stats.unsBriefCount.forEach(item => {
              backgroundColor.push(this.backgroundColor[co]);
              borderColor.push(this.borderColor[co]);
              co++;
              labels.push(this.strToArray(item.law_brief, 30));
              datas.push(item.count);

              sum = sum > item.count ? item.count : sum;
            });

            data = {
              labels,
              datasets: [{
                axis: 'y',
                label: 'Dava Sayısı',
                data: datas,
                backgroundColor,
                borderColor,
                borderWidth: 1,
                datalabels: {
                  align: 'end',
                  anchor: 'end',
                }
              }]
            };

            config = {
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
                  },
                  x: {
                    suggestedMin: 0,
                    suggestedMax: (sum+10)
                  }
                },
                
                plugins: {
                  legend: {
                    display: false
                  },
                  title: {
                    display: true,
                    text: 'Sendika Konularına Göre Dava Sayısı'
                  }
                },
              },
            };

            const myChart2 = new Chart(ctx2, config);
            break;
          case 3:
            const ctx3 = document.getElementById('stats'+i).getContext('2d');

            if(this.stats.thrBriefCount.length > 0) {
              
              this.stats.thrBriefCount.forEach(item => {
                backgroundColor.push(this.backgroundColor[co]);
                borderColor.push(this.borderColor[co]);
                co++;
                labels.push(this.strToArray(item.law_brief, 30));
                datas.push(item.count);

                sum = sum > item.count ? item.count : sum;
              });

              data = {
                labels,
                datasets: [{
                  axis: 'y',
                  label: 'Dava Sayısı',
                  data: datas,
                  backgroundColor,
                  borderColor,
                  borderWidth: 1,
                  datalabels: {
                    align: 'end',
                    anchor: 'end',
                  }
                }]
              };

              config = {
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
                    },
                    x: {
                      suggestedMin: 0,
                      suggestedMax: (sum+10)
                    }
                  },
                  
                  plugins: {
                    legend: {
                      display: false
                    },
                    title: {
                      display: true,
                      text: 'Bireysel Konularına Göre Dava Sayısı'
                    }
                  },
                },
              };

              const myChart3 = new Chart(ctx3, config);
            }
            break;
          case 4:
            const ctx4 = document.getElementById('stats'+i).getContext('2d');

            if(this.stats.unsCount.length > 0) {
              
              this.stats.unsCount.forEach(item => {
                backgroundColor.push(this.backgroundColor[co]);
                borderColor.push(this.borderColor[co]);
                co++;
                labels.push(this.strToArray(item.uns_name, 30));
                datas.push(item.count);

                sum = sum > item.count ? item.count : sum;
              });

              data = {
                labels,
                datasets: [{
                  axis: 'y',
                  label: 'Dava Sayısı',
                  data: datas,
                  backgroundColor,
                  borderColor,
                  borderWidth: 1,
                  datalabels: {
                    align: 'end',
                    anchor: 'end',
                  }
                }]
              };

              config = {
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
                    },
                    x: {
                      suggestedMin: 0,
                      suggestedMax: (sum+10)
                    }
                  },
                  
                  plugins: {
                    legend: {
                      display: false
                    },
                    title: {
                      display: true,
                      text: 'Sendikaların Dava Sayısı'
                    }
                  },
                },
              };

              const myChart4 = new Chart(ctx4, config);
            }
            break;
        
          default:
            break;
        }
      }
    }
  },
  created(){
    this.setRoutes(this.pproutes);
    this.setErrors(this.pperrors);
  },
  mounted() {
    this.createStats();
  }
}
</script>

<style>
.show-stat {
  width: 125px;
  margin-left: 10px;
}
</style>