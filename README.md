<!-- 画像の削除コマンドを独自作成 -->
※データベースには保存が記録されていないが、
　ストレージに保存されている画像を
　以下のコマンドで一斉に削除する事が可能。

　php artisan image:delete


<!-- 画像の削除タスクを登録 -->
<!-- ※上記の画像削除コマンドを以下の通りにスケジュール化済み。

　時間帯：毎日0時00分　
　コマンド： php artisan image:delete -->

<!-- コミットルール -->
1.作業スケジュール表もしくは課題表に記載のタスクを1つ終えるごとにコミットする

2.作業の過程でDBのマイグレーションファイルを修正した場合は分けてコミットする

3.細かい修正を加えた場合は、その都度コミットをする(例:1ファイルの不要なコメントを1行削除したなどでもコミットする)
　※コミットのコメントに他の方が読んでも分かる内容であれば、一括でコミットしてもOK

※コミットのコメントは何の内容であるかを明記するように留意する