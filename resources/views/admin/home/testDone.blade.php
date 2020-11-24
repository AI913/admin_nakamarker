@extends('admin.layouts.app')

@section('app_css')
    {{-- Header --}}
    <link type="text/css" rel="stylesheet" href="https://cdn.firebase.com/libs/firebaseui/3.5.2/firebaseui.css" />
    <style>h1{text-align: center;}</style>
@endsection

@section('app_title')
    {{-- タイトル --}}
    Home
@endsection

@section('app_style')
@endsection

@section('app_bread')
    {{-- パンくず --}}
    <li class="breadcrumb-item">Home Firebase</li>
@endsection

@section('app_content')
    
    {{-- メッセージModal読み込み --}}
    @include('admin.layouts.components.message', ['title' => 'メッセージ'])

    {{-- 削除確認Modal読み込み --}}
    @include('admin.layouts.components.confirm', [
        'id' => 'confirm_modal', 'title' => '削除確認', 'message' => '対象データを削除します。よろしいですか？', 'btn_id' => 'btn_remove'
    ])

    {{-- 削除Form読み込み --}}
    @include('admin.layouts.components.remove_form', ['url' => url('admin/user/remove')])

    {{-- Firebaseログインページデザイン --}}
    <a href="{{ route('admin/testLogin') }}"><button class="btn btn-primary">戻る</button></a>
    <h1>...Please wait</h1>
    <div id="info"></div>

    {{-- UIDと紐づける用のユーザIDをセット --}}
    <input type="hidden" id="user_id" name="user_id" value="{{ \Auth::user()->id }}">

@endsection

@section('app_js')
    {{-- Firebaseログインページ用Js --}}
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-auth.js"></script>
    <script src="{{ asset('js/firebase/config.js') }}"></script>
    <script>
        firebase.auth().onAuthStateChanged( (user) => {
            let h1   = document.querySelector('h1');
            let info = document.querySelector('#info');

            if(user) {
                h1.innerText   = 'Login Complete!';
                info.innerHTML = `${user.phoneNumber}さんがログインしました<br>` +
                                `(${user.uid})`;
                console.log(user);

                // UIDをユーザに紐づけ
                $.ajax({
                    url:    '/api/user/register',
                    type:   'POST',
                    dataType: 'json',
                    headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data:   {
                        'id': $('#user_id').val(),
                        'firebase_uid': user.uid,
                    }
                }).done(function(response){
                    console.log('success!')
                })

            } else {
                h1.innerText = 'Not Login';
            }
        });
    </script>
@endsection



  <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/5.8.1/firebase-auth.js"></script>
  <script src="/js/config.js"></script>
  <script>
    firebase.auth().onAuthStateChanged( (user) => {
      let h1   = document.querySelector('h1');
      let info = document.querySelector('#info');

      if(user) {
        h1.innerText   = 'Login Complete!';
        info.innerHTML = `${user.phoneNumber}さんがログインしました<br>` +
                         `(${user.uid})`;
        console.log(user);
      }
      else {
        h1.innerText = 'Not Login';
      }
    });
  </script>
</body>
</html>