<x-layouts._forums>
    <div class="p-2">
        <h1 class="text-4xl tracking-wide font-extrabold tracking-wide my-5">Log in</h1>

        <form action="{{route('login')}}" method="POST">
            @csrf
            <div
                class="w-full py-4 bg-red-alert rounded mb-3 shadow-md border-l-2 border-red-alert-text font-light text-red-alert-text pl-5 flex items-center">

                <span class="fas fa-ban mr-3"></span>
                <p>invalid password</p>
            </div>
            <!-- @if($errors->any)
            @foreach($errors->all() as $error)
            <div class="w-full py-4 bg-red-alert">
                {{$error}}
            </div>
            @endforeach
            @endif -->
            <div class=" border border-blue-form-border rounded flex flex-col flex-wrap">
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden  smaller:flex items-center pt-4 pb-8 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 ">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Your name or email:</label>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 pt-4 pb-8 px-6">
                        <label class="text-gray-800 text-sm  tracking-wide smaller:hidden mb-2" for="email">Your name or
                            email</label>
                        <input id="email"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="text" required>
                    </div>
                </div>
                <!-- ROW -->
                <div class="flex items-stretch">
                    <!-- LEFT -->
                    <div
                        class="hidden smaller:flex pt-1 pb-3 w-2/6 bg-blue-form-side border-r border-blue-form-border px-6 text-right">
                        <div class="flex-grow text-right">
                            <label class="text-gray-800 text-sm  tracking-wide" for="email">Password:</label>
                        </div>
                    </div>
                    <!-- RIGHT -->
                    <div class="flex flex-col justify-center w-full smaller:w-4/6 px-6">
                        <label class="smaller:hidden mb-2 text-gray-800 text-sm  tracking-wide"
                            for="password">Password</label>
                        <input id="password"
                            class=" bg-blue-form-input border border-blue-form-border rounded py-1 px-3  w-full focus:outline-none focus:bg-white"
                            type="password" required>
                        <span class="text-sm hover:underline text-blue-form-link">Forgot your password? </span>
                        <div class="mt-6 mb-4 pl-1 flex flex-row-reverse items-center text-gray-900 text-sm">
                            <label class="leading-none flex-grow" for="stayLoggedIn">Stay logged in</label>
                            <input class="mr-2" type="checkbox" id="stayLoggedIn">
                        </div>
                    </div>
                </div>
                <!-- BUTTON -->
                <div class="bg-blue-form-bottom smaller:flex ">
                    <div class="smaller:w-2/6"></div>
                    <div
                        class="w-full smaller:w-4/6 py-3 smaller:py-0 smaller:pl-6 bg-blue-form-bottom flex items-center justify-center smaller:justify-start">
                        <button
                            class="block py-2 px-12 font-bold bg-blue-form-button text-white rounded hover:bg-blue-form-hover-button"
                            type="submit">Log
                            in</button>
                    </div>

                </div>
            </div>



        </form>
    </div>
    <!-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-smaller-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row">
                                <label for="email"
                                    class="col-smaller-4 col-form-label text-smaller-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-smaller-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password"
                                    class="col-smaller-4 col-form-label text-smaller-right">{{ __('Password') }}</label>

                                <div class="col-smaller-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-smaller-6 offset-smaller-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-smaller-8 offset-smaller-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

</x-layouts._forums>