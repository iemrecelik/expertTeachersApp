<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link logo">
    <img src="{{asset('images/logo/meb-logo.png')}}" alt="OYGM Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">OYGM KARİYER</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->
    {{-- <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="image">
        <img src="{{asset('images/adminlte/img/user2-160x160.jpg')}}" class="img-circle elevation-2" alt="User Image">
      </div>
      <div class="info">
        <a href="#" class="d-block">Admin BEY</a>
      </div>
    </div> --}}

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
              with font-awesome or any other icon font library -->
        <li class="nav-item">
          <a href="http://expertteachersapp.test/main" class="nav-link">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Ana Sayfa
            </p>
          </a>
        </li>

        <li class="nav-item data-menu-open">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-search"></i>
            <p>
               Uzmanlık Arama
               <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li>
              <!-- SidebarSearch Form -->
              <div class="form-inline mt-3 mb-2">
                <div class="input-group" data-widget="sidebar-search">
                  <input class="form-control form-control-sidebar" type="search" placeholder="Tc Kimlik No" aria-label="Search">
                  <div class="input-group-append">
                    <button class="btn btn-sidebar">
                      <i class="fas fa-search fa-fw"></i>
                    </button>
                  </div>
                </div>
              </div>
            </li>
            <li class="nav-item">
              <a href="{{route('admin.search.teacherInfos')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Uzmanlık Arama</p>
              </a>
            </li>
          </ul>
        </li>
        
        <li class="nav-item data-menu-open">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-search"></i>
            <p>
               Evrak Yönetimi
               <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{route('admin.document_mng.category.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Kategori Listesi</p>
              </a>
            </li>
            
            <li class="nav-item">
              <a href="{{route('admin.document_mng.list.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Liste Yönetimi</p>
              </a>
            </li>

            <li class="nav-item">
              <a href="{{route('admin.document_mng.comment.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Evrak Notları</p>
              </a>
            </li>
            
            <li class="nav-item">
              <a href="{{route('admin.document_mng.document.create')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Evrak Ekleme</p>
              </a>
            </li>
            
            {{-- <li class="nav-item">
              <a href="{{route('admin.document_mng.document.manualCreate')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Manual Evrak Ekleme</p>
              </a>
            </li> --}}

            <li class="nav-item">
              <a href="{{route('admin.document_mng.search.searchForm')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Evrak Listeleme</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item data-menu-open">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-search"></i>
            <p>
               Davalar Yönetimi
               <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{route('admin.lawsuit_mng.lawsuits.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Davalar Listesi</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('admin.lawsuit_mng.statistical.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Dava İstatistikleri</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item data-menu-open">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-search"></i>
            <p>
               Öğretmenler Liste Yönetimi
               <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li>
              <!-- SidebarSearch Form -->
              <div class="form-inline mt-3 mb-2">
                <form action="{{route('admin.teachers.infos')}}" method="post">
                  @csrf
                  <div class="input-group">
                    <input class="form-control form-control-sidebar" 
                      type="search" 
                      name="thr_tc_no"
                      placeholder="Tc Kimlik No" 
                      aria-label="Search"
                    >
                    <div class="input-group-append">
                      <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                      </button>
                    </div>
                  </div>
                </form>
              </div>
            </li>
            <li class="nav-item">
              <a href="{{route('admin.teachers.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Öğretmenler Listesi</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="{{route('admin.institutions.index')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Kurum Listesi</p>
              </a>
            </li>
          </ul>
        </li>

        <li class="nav-item">
          <a href="{{route('admin.unions.index')}}" class="nav-link">
            <i class="far fa-circle nav-icon"></i>
            <p>Sendika Yönetimi</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fas fa-table"></i>
            <p>
              Eski Yönetmelik
              <i class="fas fa-angle-left right"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="{{route('admin.old_regulation.search')}}" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Uzmanlık Arama</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/simple.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Tüm Sınava Girenler</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/data.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Başvuru(2006)</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/jsgrid.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Başvuru(2011)</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/jsgrid.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sertifika Alanlar</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/jsgrid.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Sertifika İade</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="pages/tables/jsgrid.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Mahkeme Kararları</p>
              </a>
            </li>

          </ul>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>