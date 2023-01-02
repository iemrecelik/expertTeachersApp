<template>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Profile</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">User Profile</li>
          </ol>
        </div>
      </div>
    </div><!-- /.container-fluid -->
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-3">

          <profile-component></profile-component>

          <!-- About Me Box -->
          <!-- <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">About Me</h3>
            </div> -->
            <!-- /.card-header -->
            <!-- <div class="card-body">
              <strong><i class="fas fa-book mr-1"></i> Education</strong>

              <p class="text-muted">
                B.S. in Computer Science from the University of Tennessee at Knoxville
              </p>

              <hr>

              <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

              <p class="text-muted">Malibu, California</p>

              <hr>

              <strong><i class="fas fa-pencil-alt mr-1"></i> Skills</strong>

              <p class="text-muted">
                <span class="tag tag-danger">UI Design</span>
                <span class="tag tag-success">Coding</span>
                <span class="tag tag-info">Javascript</span>
                <span class="tag tag-warning">PHP</span>
                <span class="tag tag-primary">Node.js</span>
              </p>

              <hr>

              <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>

              <p class="text-muted">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam fermentum enim neque.</p>
            </div> -->
            <!-- /.card-body -->
          <!-- </div> -->
          <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="card">
            <div class="card-header p-2">
              <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="pills-home-tab" 
                    data-toggle="pill" 
                    data-target="#pills-home" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-home" 
                    aria-selected="true"
                  >
                    Eski Yönetmelik
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-profile-tab" 
                    data-toggle="pill" 
                    data-target="#pills-profile" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-profile" 
                    aria-selected="false"
                  >
                    Yeni Yönetmelik
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-correspondence-tab" 
                    data-toggle="pill" 
                    data-target="#pills-correspondence" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-correspondence" 
                    aria-selected="false"
                  >
                    Yazışmalar
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="pills-update-profile-tab" 
                    data-toggle="pill" 
                    data-target="#pills-update-profile" 
                    type="button" 
                    role="tab" 
                    aria-controls="pills-update-profile" 
                    aria-selected="false"
                  >
                    Bilgileri Güncelle
                  </button>
                </li>
              </ul>
            </div><!-- /.card-header -->
            <div class="card-body">

              <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                  <old-regulation-component></old-regulation-component>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                  <new-regulation-component></new-regulation-component>
                </div>
                <div class="tab-pane fade" id="pills-correspondence" role="tabpanel" aria-labelledby="pills-correspondence-tab">
                  <update-knowlange-component></update-knowlange-component>
                </div>
                <div class="tab-pane fade" id="pills-update-profile" role="tabpanel" aria-labelledby="pills-update-profile-tab">
                  <update-knowlange-component></update-knowlange-component>
                </div>
              </div>

             <!--  <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab" tabindex="0">
                  <old-regulation-component></old-regulation-component>
                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab" tabindex="0">
                  <new-regulation-component></new-regulation-component>
                </div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab" tabindex="0">
                  <update-knowlange-component></update-knowlange-component>
                </div>
              </div> -->
              <!-- /.tab-content -->
            </div><!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div><!-- /.container-fluid -->
  </section>
  <!-- /.content -->
</div>
</template>

<script>
import ProfileComponent from "./ProfileComponent.vue";
import OldRegulationComponent from "./OldRegulationComponent.vue";
import NewRegulationComponent from "./NewRegulationComponent.vue";
import UpdateKnowlangeComponent from "./UpdateKnowlangeComponent.vue";

export default {
  name: 'TeacherInfosComponent',
  components: {
    "profile-component": ProfileComponent,
    "old-regulation-component": OldRegulationComponent,
    "new-regulation-component": NewRegulationComponent,
    "update-knowlange-component": UpdateKnowlangeComponent,
  }
}
</script>

<style>

</style>