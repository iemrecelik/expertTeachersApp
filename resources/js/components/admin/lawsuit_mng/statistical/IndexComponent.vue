<template>
<template-component>
  <form class="mb-3" :action="routes.index" method="get">
    <div class="row">
      <div class="col-12">
        <div class="form-group">
          <label for="exampleInputEmail1">
            {{ $t('messages.year_list') }}
          </label>
          <treeselect
            name="years[]"
            :multiple="true"
            :options="yearList"
            v-model="years"
            :disable-branch-nodes="false"
            :show-count="true"
            :placeholder="$t('messages.enter_showing_years')"
          />
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-3">
        <button type="submit" class="btn btn-md btn-primary">
          Listele
        </button>
      </div>
    </div>
  </form>

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

  <form id="show-stats-pdf" :action="routes.statsToPdf" method="POST" target="_blank">
    
    <input type="hidden" name="_token" :value="token">
    <input id="stats-html" type="hidden" name="statsHtml">
    <input id="stats-html" type="hidden" name="statsCss" :value="statsCss">

    <div class="row">
      <div class="col-12 table-wrapper">
        <table class="fl-table table table-striped table-inverse table-responsive table-bordered">
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

        <div class="row-total-stats row">
          <div class="col-total-stats col-3">
            <div class="show-stat float-left float-left">
              <b>Bireysel Davalar </b>
            </div>
            <div class="float-left">
              <span>: {{ stats.thrSum }}</span>
            </div>
          </div>
        </div>

        <div class="row-total-stats row mt-1">
          <div class="col-total-stats col-3">
            <div class="show-stat float-left">
              <b>Sendika Davalar </b>
            </div>
            <div class="float-left">
              <span>: {{ stats.unsSum }}</span>
            </div>
          </div>
        </div>

        <div class="row-total-stats row mt-1">
          <div class="col-total-stats col-3">
            <div class="show-stat float-left">
              <b>Toplam Davalar </b>
            </div>
            <div class="float-left">
              <span>: {{ (stats.unsSum + stats.thrSum) }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    

    <div class="row mt-3">
      <div class="col-1">
        <select class="form-control" name="statsLandscape" id="">
          <option value="L">Yatay</option>
          <option value="">Dikey</option>
        </select>
      </div>
      <div class="col-1">
        <button type="submit" class="btn btn-block btn-primary btn-flat">
          PDF Çıktısı
        </button>
      </div>
    </div>
  </form>

</template-component>
</template>

<script>
import Treeselect from '@riophae/vue-treeselect';
// import the styles
import '@riophae/vue-treeselect/dist/vue-treeselect.css';

import { Chart, registerables } from 'chart.js';
import ChartDataLabels from 'chartjs-plugin-datalabels';
Chart.register(ChartDataLabels, ...registerables);

import { mapState, mapMutations } from 'vuex';

export default {
  name: 'IndexComponent',
  data () {
    return {
      stats: this.ppstats,
      years: this.ppyears ?? null,
      yearList: [],
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
      statsCss: `
        t*{
            box-sizing: border-box;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        body{
            font-family: Helvetica;
            -webkit-font-smoothing: antialiased;
        }

        /* Table Styles */

        .table-wrapper{
            margin: 10px 70px 70px;
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
      `
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
    ppyears: {
      type: Array,
      required: false,
    },
  },
  computed: {
    ...mapState([
      'routes',
      'errors',
      'token'
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
    createStats: async function() {

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
                    barPercentage: 0.1,
                  },
                  x: {
                    suggestedMin: 0,
                    suggestedMax: (sum+3),
                    ticks: {
                      stepSize: 1
                    }
                  },
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
                    suggestedMax: (sum+3),
                    ticks: {
                      stepSize: 1
                    }
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
                      suggestedMax: (sum+3),
                      ticks: {
                        stepSize: 1
                      }
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
                      suggestedMax: (sum+3),
                      ticks: {
                        stepSize: 1
                      }
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

    for (let i = 2022; i < 2045; i++) {
      this.yearList.push({
        id: i,
        label: i
      });
    }

    /* setTimeout(() => {
      this.ppyears.forEach(year => {
        this.years.push(parseInt(year));
      });  
    }, 3000); */
    
  },
  mounted() {
    this.createStats().then(() => {
      document.getElementById('stats-html').value = document.getElementsByClassName('table-wrapper')[0].innerHTML;
    }); 
  },
  components: {
    Treeselect
  }
}
</script>

<style>
.show-stat {
  width: 125px;
  /* margin-left: 10px; */
}
</style>