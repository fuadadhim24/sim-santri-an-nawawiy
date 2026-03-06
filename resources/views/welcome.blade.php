<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>An-Nawawiy</title>

    <link rel="shortcut icon" href="image/pondok.png" type="image/x-icon" />
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous" />
    <link href="style.css" rel="stylesheet" />

    <!-- font montserrat -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700;800&family=Poppins:wght@300;400;700&display=swap" rel="stylesheet" />
    <!-- font awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
      integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <!-- jqueary -->
    <script
      src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js"
      integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    ></script>
  </head>
  <body>
    <!-- NAVBAR START -->
    <nav class="navbar navbar-expand-lg fixed-top navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand fw-bold fs-6" href="#"><img src="image/pondok.png" width="50" />An-Nawawiy</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDarkDropdown" aria-controls="navbarNavDarkDropdown" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse d-lg-flex justify-content-between" id="navbarNavDarkDropdown">
          <ul class="navbar-nav nav-pills mx-auto mb-2 mb-lg-0 text-center flex-grow-1 justify-content-center align-items-center">
            <li class="nav-item">
              <a class="nav-link active mx-1" style="background-color: green" aria-current="page" href="#">Home</a>
            </li>
            <!-- dropdown 1 start -->
            <li class="nav-item dropdown mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Profile </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="/Sejarah-Pondok/index.html">Sejarah</a></li>
                <li><a class="dropdown-item" href="#">Visi dan Misi</a></li>
                <li><a class="dropdown-item" href="#">Struktur Oraganisasi</a></li>
                <li><a class="dropdown-item" href="/Profile-Pengasuh/index.html">Pengasuh</a></li>
              </ul>
            </li>
            <!-- dropdown 1 end -->
            <!-- dropdown 2 start -->
            <li class="nav-item dropdown mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Lembaga </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="#">TPQ</a></li>
                <li><a class="dropdown-item" href="/SMP/index.html">SMP</a></li>
                <li><a class="dropdown-item" href="#">SMA</a></li>
                <li><a class="dropdown-item" href="#">PPTQ</a></li>
              </ul>
            </li>
            <!-- dropdown 2 end -->
            <!-- dropdown 3 start -->
            <li class="nav-item dropdown mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Informasi </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="#">Kajian</a></li>
                <li><a class="dropdown-item" href="#">Berita Pondok</a></li>
                <li><a class="dropdown-item" href="#">Pengumuman</a></li>
              </ul>
            </li>
            <!-- dropdown 3 end -->
             <!-- dropdown 4 start -->
            <li class="nav-item dropdown mx-1">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Santri </a>
              <ul class="dropdown-menu dropdown-menu-dark">
                <li><a class="dropdown-item" href="/Dokumentasi-Acara/index.html">Dokumentasi Acara</a></li>
                <li><a class="dropdown-item" href="#">Diskusi Santri</a></li>
              </ul>
            </li>
            <!-- dropdown 4 end -->
            <li class="nav-item">
              <a class="nav-link mx-1" href="#">Pendaftaran</a>
            </li>
          </ul>
          <div class="d-flex flex-column flex-lg-row align-items-center gap-2 ms-lg-auto">
            @if (Route::has('login'))
              @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
              @else
                <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm me-2">Login</a>
                <a href="{{ route('register') }}" class="btn btn-light btn-sm text-dark">Daftar Wali Santri</a>
              @endauth
            @endif
          </div>
        </div>
      </div>
    </nav>
    <!-- test -->

    <!-- test -->
    <!-- NAVBAR END -->
    <!-- HERO START -->
    <div class="hero d-flex align-items-center">
      <div class="container">
        <div class="row row-cols-lg-2 row-cols-1">
          <div class="col-lg-7 col-sm-12">
            <h1 class="display-1 fw-bold text-white mb-4">PPTQ<br />AN-NAWAWIY</h1>
            <p class="text-white" style="opacity: 88%">Pondok Pesantren dengan program menghafal Al - Qur'an serta memiliki pengetahuan umum yang tinggi</p>
          </div>
          <!-- carousel start -->
          <div class="col-sm-12 col-lg-5">
            <div class="card">
              <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                  <div class="carousel-item active">
                    <img src="image/hero-1_2_11zon.jpg" class="d-block w-100"/>
                  </div>
                  <div class="carousel-item">
                    <img src="image/hero-2_1_11zon.jpg" class="d-block w-100" alt="..." />
                  </div>
                  <div class="carousel-item">
                    <img src="image/halaman3.jpg" class="d-block w-100" alt="..." />
                  </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              </div>
            </div>
          </div>
          <!-- carousel end -->
        </div>
      </div>
    </div>

    <!-- HERO END -->
    <!-- ABOUT START -->
    <div class="about">
      <div class="container-fluid">
        <div class="row row-cols-lg-3 row-cols-1">
          <div class="col-jumlahsantri text-center py-5 text-white bg-dark">
            <h3 class="fw-bold">Jumlah Santri</h3>
            <p>di seluruhl Lembaga Pendidikan Pondok Pesantren Tahfidzul Qur'an An-Nawawiy</p>
            <div class="row justify-content-center mt-4">
              <div class="col-4">
                <img src="image/pondok.png" width="37%" class="my-3" />

              </div>
              <div class="col-4">
                <p>Santri Putra</p>
            <h5 class="mb-4">127</h5>
              </div>
              <div class="col-4">
                <p>Santri Putri</p>
            <h5 class="mb-4">124</h5>
              </div>
            </div>
            <div class="row justify-content-center mt-4">
              <div class="col-4">
                <img src="image/smpq.png" width="37%" class="my-3" />

              </div>
              <div class="col-4">
                <p>Siswa Putra</p>
            <h5 class="mb-4">93</h5>
              </div>
              <div class="col-4">
                <p>Siswa Putri</p>
            <h5 class="mb-4">72</h5>
              </div>
            </div>
            <div class="row justify-content-center mt-4">
              <div class="col-4">
                <img src="image/LOGO SMAQ.png" width="37%" class="my-3" />

              </div>
              <div class="col-4">
                <p>Siswa Putra</p>
            <h5 class="mb-4">32</h5>
              </div>
              <div class="col-4">
                <p>Siswa Putri</p>
            <h5 class="mb-4">35</h5>
              </div>
            </div>
            <br />
         </div>

          <div class="col pt-5">
  <h2 class="text-center fw-bold mb-0">Video Profil Pondok</h2>
  <p class="text-pengumuman text-center mb-3">
    <a href="#">Lihat Lainnya</a>
  </p>

  <div class="ratio ratio-16x9">
    <iframe src="https://www.youtube.com/embed/BiKLlsuEWxA"
            title="Video Profil"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
    <p></p>
      </div>
        </div>
        <!-- VIDIO PROFIL END -->
        <!-- PENGUMUMAN START -->
          <div class="col pt-5 bg-dark text-white">
            <h3 class="text-center fw-bold mb-0">Pengumuman</h3>
            <p class="text-pengumuman text-center mb-3">
              <a href="#">Semua Pengumuman</a>
            </p>
            <ul>
              <li>Pendaftaran SMP & SMA Al-Qur'an An-Nawawiy Telah dibuka <span class="fw-bold">02 Mei 2023</span>.<a class="text-dua nav-link" href="#">read more</a></li>
              <li>An-Nawawiy Festival Qur'any 2026 (AFQy) <span class="fw-bold">17 Januari 2026</span></li>
              <li>Pelaksanaan Ujian Rajab 1447 Hijriyah <span class="fw-bold">05 Januari 2026</span></li>
            </ul>
          </div>
          <!-- PENGUMUMAN END -->
        </div>
      </div>
    </div>

    <!-- ABOUT END -->
    <!-- PROFIL PENGASUH START -->

    <div class="container-fluid">
      <div class="mt-5 col" id="tag-headline">
        <h2 style="font-size: 2.1rem" class="fw-bold py-2">Pengasuh Pondok</h2>
      </div>
      <div class="container mt-4">
        <div class="row justify-content-center">
          <div class="col-lg-4 col-12">
            <img src="/image/-KH.-Masrur-Yusuf-foto.jpg" style="width: 100%" />
          </div>
          <div class="col-lg-5 col-12">
            <h4 class="fw-bold">KH. Masrur Yusuf</h4>
            <p align="justify" class="">
              Beliau terlahir pada tanggal 10 Juni 1948 di Dusun Mengelo, Desa Sooko, Kecamatan Sooko Kab. Mojokerto dari pasangan suami istri, Muhammad Yusuf dan Sa’diyah. Muhammad Yusuf adalah seorang pengusaha serabutan, tetapi ia
              kemudian sukses dalam berjualan gethuk hingga mampu mempekerjakan tetangga dikanan kirinya.
            </p>
            <a href="#" class="text-profilpengasuh"> Selengkapnya </a>
          </div>
        </div>
      </div>
    </div>

    <!-- PROFIL PENGASUH END -->

    <!-- BERITA START -->

    <div id="project" class="project">
      <div class="container-fluid">
        <div class="row">
          <div class="col" id="tag-headline">
            <h2 style="font-size: 2.1rem" class="fw-bold py-2">Berita Pondok</h2>
          </div>
        </div>
      </div>
    </div>

    <!-- highlight image start -->
    <div class="container mb-5" id="image-start">
      <div class="row row-cols-lg-2 row-cols-1">
        <div class="col-lg-8 col-sm-8 mb-4">
          <div class="card">
            <div id="carouselExampleCaptions" class="carousel slide carousel-fade" data-bs-ride="carousel">
              <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
              </div>
              <div class="carousel-inner">
                <div class="carousel-item active">
                  <img src="https://ik.imagekit.io/Annawawiy/Untitled%20design%20(3).png?f-auto" class="d-block w-100" alt="..." />
                  <div class="carousel-caption d-none d-md-block">
                  </div>
                </div>
                <div class="carousel-item ">
                  <img src="https://ik.imagekit.io/Annawawiy/Untitled%20design%20(2).png?f-auto" class="d-block w-100" alt="..." />
                  <div class="carousel-caption d-none d-md-block">
                  </div>
                </div>
                <div class="carousel-item">
                  <img src="https://ik.imagekit.io/Annawawiy/DSC04897.JPG?f-auto" class="d-block w-100" alt="..." />
                  <div class="carousel-caption d-none d-md-block">
                  </div>
                </div>
              </div>
              <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
              </button>
              <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
              </button>
            </div>
          </div>
        </div>

        <!-- highlight image end -->

        <div class="col-lg-4 col-sm-8">
          <h3 class="fw-bold text-center">Terbaru</h3>
          <hr />
          <div class="card p-2">
            <div class="card-body">
              <h5 class="fw-bold">PPDB 2026</h5>
              <p>Telah dibuka PPDB SMP & SMA Al-Qur'an An-Nawawiy 2026</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div>
          <div class="card p-2">
            <div class="card-body">
              <h5 class="fw-bold">Juara MHQ Tingkat Nasional</h5>
              <p>Siswa SMP Al-Qur'an An-Nawawiy berhasil mendapatkan juara 1, 2 & dan 3....
              </p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div>
          <div class="card p-2">
            <div class="card-body">
              <h5 class="fw-bold">AFQy 2026</h5>
              <p>An-Nawawiy Festival Qur'any sukses dilaksanakan pada hari Sabtu, 17 Januari 2026</p>
              <a href="#" class="stretched-link"></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- HIGHLIGHT BERITA END -->

    <!-- BERITA START -->
    <!-- <div class="container">
      <div class="card-content">
        <div class="row row-cols-lg-4 row-cols-2">
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">1</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">1</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">1</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">1</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">2</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">2</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">2</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-sm-6">
            <div class="card">
              <a href="#"><img src="image/halaman.JPG" class="w-100 p-2" alt="" /></a>
              <div class="card-body">
                <h5 class="card-title">2</h5>
                <p class="card-text">Ujian ini akan dilaksanakan pada tangal 12 Desember 2022</p>
                <a href="#" class="stretched-link aria-hidden"></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="pagination-indi">
      <li class="page-item previous-page disable"><a class="page-link" href="#">Prev</a></li>
      <li class="page-item current-page active"><a class="page-link" href="#">1</a></li>
      <li class="page-item dots"><a class="page-link" href="#">...</a></li>
      <li class="page-item current-page"><a class="page-link" href="#">5</a></li>
      <li class="page-item current-page"><a class="page-link" href="#">6</a></li>
      <li class="page-item dots"><a class="page-link" href="#">...</a></li>
      <li class="page-item current-page"><a class="page-link" href="#">10</a></li>
      <li class="page-item next-page"><a class="page-link" href="#">Next</a></li>
    </div> -->
    <!-- BERITA END -->

    <!-- KONTEN PONDOK START -->
    <div class="container mb-5">
      <div class="row row-cols-lg-2 row-cols-1">
        <div class="col-lg-10 col-sm-12">
          <h2 style="font-size: 2.5rem" class="text-dokumentasi">Konten Pondok.</h2>
        </div>

        <div class="col-lg-2 col-sm-12 align-self-center">
          <a href="/Dokumentasi-Acara/index.html" class="text-dokumentasi-lainnya text-white">Lihat Semua</a>
        </div>
      </div>
    </div>

    <!-- image start -->
    <div class="container mb-4">
      <div class="row">
        <div class="col-lg-3 col-sm-6">
          <div class="card m-2">
            <iframe src="https://www.youtube.com/embed/YyzoYfm7dmU?si=eW-uMS-YFXnJ6SqH"
            title="Video Profil"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
            <div class="card-body">
              <h5 class="card-title">Murojaah Bersama Juz 1</h5>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card m-2">
            <iframe src="https://www.youtube.com/embed/28kYfZOTqdw?si=LW3jNW2An85124Gd"
            title="Video Profil"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
            <div class="card-body">
              <h5 class="card-title">Murojaah Bersama Juz 2</h5>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card m-2">
            <iframe src="https://www.youtube.com/embed/wURHZXUbVZI?si=jZpEzp_53E6M8dzP"
            title="Video Profil"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
            <div class="card-body">
              <h5 class="card-title">Murojaah Bersama Juz 3</h5>
            </div>
          </div>
        </div>
        <div class="col-lg-3 col-sm-6">
          <div class="card m-2">
            <iframe src="https://www.youtube.com/embed/https://youtu.be/tu3zgvOTwhY?si=8ekd3V9onD-M42oF"
            title="Video Profil"
            frameborder="0"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
            allowfullscreen>
    </iframe>
            <div class="card-body">
              <h5 class="card-title">Murojaah Bersama Juz 4</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- KONTEN PONDOK END -->

    <footer class="text-center text-lg-start bg-dark text-white">
      <!-- Section: Social media -->
      <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
        <!-- Left -->
        <div class="ms-5 d-none d-lg-block fw-bold">
          <span>Pondok Pesantren Tahfizul Quran An-Nawawiy</span>
        </div>
        <!-- Left -->

        <!-- Right -->
        <div>
          <div class="dropdown dropdown-footer rounded-3">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"> Hubungi Kami </a>
            <ul class="dropdown-menu dropdown-menu-dark">
              <li><a class="dropdown-item" href=" https://api.whatsapp.com/send?phone=085746644232" target="_blank">Ustadz Huda</a></li>
              <li><a class="dropdown-item" href="https://api.whatsapp.com/send?phone=08563195659" target="_blank">Ustadzah Okta</a></li>
            </ul>
          </div>
          <!-- <a href="" class="logo-sosial logo-sosial-1 me-4 text-reset">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="" class="logo-sosial logo-sosial-2 me-4 text-reset">
            <i class="fab fa-twitter"></i>
          </a>
          <a href="" class="logo-sosial logo-sosial-3 me-4 text-reset">
            <i class="fab fa-google"></i>
          </a>
          <a href="" class="logo-sosial logo-sosial-4 me-4 text-reset">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="" class="logo-sosial logo-sosial-5 me-4 text-reset">
            <i class="fab fa-youtube"></i>
          </a> -->
        </div>
        <!-- Right -->
      </section>
      <!-- Section: Social media -->

      <!-- Section: Links  -->
      <section class="">
        <div class="container text-center text-md-start mt-5">
          <!-- Grid row -->
          <div class="row mt-3">
            <!-- Grid column -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
              <!-- Content -->
              <i><img src="image/smpq.png" width="67" class="mb-3" /></i>
              <h6 class="text-uppercase fw-bold mb-3"><i class=""></i>SMP AL-QUR'AN AN-NAWAWIY</h6>
              <hr />
              <p>SMP Al Qur'an An-Nawawiy berlokasi di Mengelo - Sooko - Mojokerto</p>
            </div>
            <!-- Grid column -->
            <div class="col-md-3 col-lg-4 col-xl-3 mx-auto mb-4">
              <!-- Content -->
              <i><img src="image/LOGO SMAQ.png" width="67" class="mb-3" /></i>
              <h6 class="text-uppercase fw-bold mb-3"><i class=""></i>SMA AL-QUR'AN AN-NAWAWIY</h6>
              <hr />
              <p>SMA Al Qur'an An- Nawawiy berlokasi di Mengelo - Sooko - Mojokerto</p>
            </div>
            <!-- Grid column -->

            <!-- Grid column -->

            <!-- Grid column -->

            <!-- Grid column -->

            <!-- Grid column -->
            <div class="col-md-4 col-lg-4 col-xl-3 mx-auto mb-md-0 mt-4">
              <!-- Links -->
              <h5 class="fw-bold mb-3">Lokasi Pondok</h5>
              <!--Google map-->
              <div id="map-container-google-3" class="z-depth-1-half map-container-3 align-items-center">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3955.7604045094995!2d112.42749667425213!3d-7.4916852738837285!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e780d7ded31c417%3A0x82190db1566d3d13!2sSMP%20Alquran%20An-nawawiy%20Mangelo%20mojokerto!5e0!3m2!1sen!2sus!4v1683362633997!5m2!1sen!2sus"
                  width="400"
                  height="300"
                  style="border: 0"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                ></iframe>
              </div>
            </div>
            <!-- Grid column -->
          </div>
          <!-- Grid row -->
        </div>
      </section>
      <!-- Section: Links  -->

      <!-- Copyright -->
      <div class="d-flex justify-content-center mt-4" style="background-color: rgba(0, 0, 0, 0.05)">
         <div class="logo-navbar d-flex gap-4 align-items-center">
              <a class="nav-link logo-navbar-1 fa-brands fa-youtube fs-5" href="https://www.youtube.com/@PPTQAn-Nawawiy" target="_blank"> </a>

              <a class="nav-link logo-navbar-2 fa-brands fa-instagram fs-5" href="https://www.instagram.com/pptq.annawawiy.mojokerto" target="_blank"> </a>

              <a class="nav-link logo-navbar-3 fa-brands fa-facebook fs-5" href="https://www.facebook.com/profile.php?id=100090290989902&mibextid=ZbWKwL" target="_blank"> </a>

              <a class="nav-link logo-navbar-4 fa-brands fa-tiktok fs-5" href="https://www.tiktok.com/@pptq.annawawiy.mojokerto" target="_blank"> </a>
            </div>
             <div class="text-center p-4">
        © 2023 Copyright:
        <a class="text-reset fw-bold" href="#">IT An - Nawawiy</a>
      </div>

      </div>

      <!-- Copyright -->
    </footer>

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script type="text/javascript">
      function getPageList(totalPages, page, maxLength) {
        function range(start, end) {
          return Array.from(Array(end - start + 1), (_, i) => i + start);
        }

        var sideWidth = maxLength < 9 ? 1 : 2;
        var leftWidth = (maxLength - sideWidth * 2 - 3) >> 1;
        var rightWidth = (maxLength - sideWidth * 2 - 3) >> 1;

        if (totalPages <= maxLength) {
          return range(1, totalPages);
        }

        if (page <= maxLength - sideWidth - 1 - rightWidth) {
          return range(1, maxLength - sideWidth - 1).concat(0, range(totalPages - sideWidth + 1, totalPages));
        }

        if (page >= totalPages - sideWidth - 1 - rightWidth) {
          return range(1, sideWidth).concat(0, range(totalPages - sideWidth - 1 - rightWidth - leftWidth, totalPages));
        }

        return range(1, sideWidth).concat(0, range(page - leftWidth, page + rightWidth), 0, range(totalPages - sideWidth + 1, totalPages));
      }

      $(function () {
        var numberOfItems = $(".card-content .card").length;
        var limitPerPage = 4; //How many card items visible per a page
        var totalPages = Math.ceil(numberOfItems / limitPerPage);
        var paginationSize = 7; //How many page elements visible in the pagination
        var currentPage;

        function showPage(whichPage) {
          if (whichPage < 1 || whichPage > totalPages) return false;

          currentPage = whichPage;

          $(".card-content .card")
            .hide()
            .slice((currentPage - 1) * limitPerPage, currentPage * limitPerPage)
            .show();

          $(".pagination-indi li").slice(1, -1).remove();

          getPageList(totalPages, currentPage, paginationSize).forEach((item) => {
            $("<li>")
              .addClass("page-item")
              .addClass(item ? "current-page" : "dots")
              .toggleClass("active", item === currentPage)
              .append(
                $("<a>")
                  .addClass("page-link")
                  .attr({ href: "javascript:void(0)" })
                  .text(item || "...")
              )
              .insertBefore(".next-page");
          });

          $(".previous-page").toggleClass("disable", currentPage === 1);
          $(".next-page").toggleClass("disable", currentPage === totalPages);
          return true;
        }

        $(".pagination-indi").append(
          $("<li>")
            .addClass("page-item")
            .addClass("previous-page")
            .append($("<a>").addClass("page-link").attr({ href: "javascript:void(0)" }).text("Prev")),
          $("<li>")
            .addClass("page-item")
            .addClass("next-page")
            .append($("<a>").addClass("page-link").attr({ href: "javascript:void(0)" }).text("Next"))
        );

        $(".card-content").show();
        showPage(1);

        $(document).on("click", ".pagination-indi li.current-page:not(.active)", function () {
          return showPage(+$(this).text());
        });

        $(".next-page").on("click", function () {
          return showPage(currentPage + 1);
        });

        $(".previous-page").on("click", function () {
          return showPage(currentPage - 1);
        });
      });
    </script>
  </body>
</html>
