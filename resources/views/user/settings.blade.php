@extends('user.layout')

@section('user_section')

@push('style')
<link href="{{ asset('css/uppy.min.css') }}" rel="stylesheet">
@endpush

<div class="page-content">
    <div class="inner-box">
        <div class="dashboard-box">
            <h2 class="dashbord-title">Configuración de cuenta</h2>
        </div>
        <div class="dashboard-wrapper">

            <div class="row form-dashboard">
                <div class="col-12">
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="privacy-box privacysetting">
                        <div class="dashboardboxtitle">
                            <h2>Preferencias de usuario:</h2>
                        </div>
                        <div class="dashboardholder mb-md-5">
                            <div class="user">
                                <div class="usercontent mt-3">
                                    <form class="" method="post" action="{{ route('update_user') }}" id="user-data">
                                        @csrf

                                        <input name="social_facebook" type="hidden" value="{{ Auth::user()->social_facebook }}">
                                        <input name="social_twitter" type="hidden" value="{{ Auth::user()->social_twitter }}">
                                        <input name="social_youtube" type="hidden" value="{{ Auth::user()->social_youtube }}">

                                        <div class="form-group mb-3">
                                            <label for="name">Su nombre:</label>
                                            <input class="form-control" name="name" value="{{ Auth::user()->name }}">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="name" class="text-left">Su celular:</label>
                                            <input class="form-control" name="phone" value="{{ Auth::user()->phone }}" type="number">
                                        </div>

                                        <div class="form-group mb-3">
                                            <label for="name" class="text-left">Firma de sus anuncios:</label>
                                            <textarea class="form-control" name="signature" rows="4">{{ Auth::user()->signature }}</textarea>
                                        </div>

                                        <button class="btn btn-common btn-block" type="submit">Actualizar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-dashboard">
                <div class="col-12">
                    <div class="privacy-box privacysetting">
                        <div class="dashboardboxtitle">
                            <h2>Su foto de perfil:</h2>
                        </div>
                        <div class="dashboardholder mb-md-5">
                            <div class="DashboardContainer"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-dashboard">
                <div class="col-12">
                    <div class="privacy-box privacysetting">
                        <div class="dashboardboxtitle">
                            <h2>Redes sociales:</h2>
                        </div>
                        <div class="dashboardholder mb-md-5">
                            <form class="" method="post" action="{{ route('update_user') }}" id="user-data">
                                @csrf

                                <input name="name" type="hidden" value="{{ Auth::user()->name }}">
                                <input name="phone" type="hidden" value="{{ Auth::user()->phone }}">
                                <input name="signature" type="hidden" value="{{ Auth::user()->signature }}">

                                <div class="form-group mb-3">
                                    <label for="name">Facebook:</label>
                                    <input class="form-control" name="social_facebook" value="{{ Auth::user()->social_facebook }}" placeholder="https://www.facebook.com/Bachecubano">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name">Twitter:</label>
                                    <input class="form-control" name="social_twitter" value="{{ Auth::user()->social_twitter }}" placeholder="https://www.twitter.com/Bachecubano">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="name">Youtube:</label>
                                    <input class="form-control" name="social_youtube" value="{{ Auth::user()->social_youtube }}" placeholder="https://www.youtube.com/Bachecubano">
                                </div>

                                <button class="btn btn-common btn-block" type="submit">Actualizar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-dashboard">
                <div class="col-12">
                    <div class="privacy-box privacysetting">
                        <div class="dashboardboxtitle">
                            <h2>Cambiar contraseña:</h2>
                        </div>
                        <div class="dashboardholder mb-md-5">
                            <div class="user">
                                <div class="usercontent mt-3">
                                    <form class="" method="post" action="{{ route('update_user_password') }}" id="user-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label for="name">Contraseña actual:</label>
                                            <input class="form-control" type="password" name="current_password" placeholder="****************">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="name">Nueva contraseña:</label>
                                            <input class="form-control" type="password" name="new_password" placeholder="****************">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="name">Repita nueva contraseña:</label>
                                            <input class="form-control" type="password" name="new_password2" placeholder="****************">
                                        </div>
                                        <button class="btn btn-common btn-block" type="submit">Actualizar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row form-dashboard">
                <div class="col-12">
                    <hr>
                    <div class="privacy-box deleteaccount">
                        <div class="dashboardboxtitle">
                            <h2>Eliminar mi cuenta:</h2>
                        </div>
                        <div class="dashboardholder">
                            <h5 class="text-center">¡Atención! Esta operación es irreversible.</h5>
                            <form action="{{ route('delete_account') }}" method="post" onsubmit="return confirm('¿Está seguro de eliminar su cuenta? ¡Última advertencia!');">
                                @csrf
                                <div class="form-group">
                                    <textarea class="form-control" placeholder="Déjanos saber en qué podemos mejorar" name="feedback"></textarea>
                                </div>
                                <button class="btn btn-danger btn-block" type="submit">Eliminar cuenta</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
<!-- AJAX Uploading for Add Post -->
<script src="{{ asset('js/uppy.min.js') }}"></script>
<script src="{{ asset('js/es_ES.min.js') }}"></script>
<script>
    const uppy = Uppy.Core({
            debug: false,
            autoProceed: true,
            restrictions: {
                maxFileSize: 600000,
                maxNumberOfFiles: 1,
                allowedFileTypes: ['.jpg', '.jpeg', '.png', '.gif']
            },
            locale: Uppy.locales.es_ES
        })
        .use(Uppy.Dashboard, {
            inline: true,
            target: '.DashboardContainer',
            replaceTargetContent: true,
            showProgressDetails: true,
            note: 'Sólo imágenes, 1 foto, de no más de 600kb',
            height: 350,
            width: '100%',
            metaFields: [{
                    id: 'photo',
                    name: 'photo',
                    placeholder: 'Nombre del fichero subido'
                },
                {
                    id: 'caption',
                    name: 'Caption',
                    placeholder: 'Describe la imagen que estás subiendo'
                }
            ],
            browserBackButtonClose: true,
            plugins: ['Webcam']
        })
        .use(Uppy.XHRUpload, {
            endpoint: "{{ route('save-profile-image-ajax') }}?api_token=" + user_token,
            formData: true,
            fieldName: 'files[]',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
    uppy.on('upload-success', (file, response) => {
        if (response.status == 200) {
            $('#user-data').submit();
        }
    })
</script>
@endpush

@endsection