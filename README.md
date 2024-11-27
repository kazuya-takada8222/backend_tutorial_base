# Partsone バックエンド研修課題リポジトリ

## 取り組んだ課題
- 基本課題①：API・DB設計
- 基本課題②：APIの実装
- 基本課題③：Seederの実装
- 応用課題①：Reducの導入
- 応用課題②：認証機能の実装

## 取り組んだ課題ごとの工夫点・アピールポイント
### 基本課題①：API・DB設計
DB設計を行う際にRDBの設計について学び、正規形となるように心がけた。また記事をカテゴリ（政治、スポーツ、エンタメ等）別に表示できたら便利だと思ったのでカテゴリのテーブルも作成した。
### 基本課題②：APIの実装
SwaggerUIを用いて動作確認を行ったところうまくいかなかった。原因を調べていくと、swaggerコンテナの中のnginxの設定ファイルnginx.confの設定が原因だとわかった。以下がもとのnginx.confである。
```
types {
    text/plain yaml;
    text/plain yml;
  }

  gzip on;
  gzip_static on;
  gzip_disable "msie6";

  gzip_vary on;
  gzip_types text/plain text/css application/javascript;

  map $request_method $access_control_max_age {
    OPTIONS 1728000; # 20 days
  }
  server_tokens off; # Hide Nginx version

  server {
    listen            $PORT;
    server_name       localhost;
    index             index.html index.htm;

    location $BASE_URL {
      absolute_redirect off;
      alias            /usr/share/nginx/html/;
      expires 1d;

      location ~ swagger-initializer.js {
        expires -1;
        include templates/cors.conf;
      }

      location ~* \.(?:json|yml|yaml)$ {
        #SWAGGER_ROOT
        expires -1;

        include templates/cors.conf;
      }

      include templates/cors.conf;
      include templates/embedding.conf;
    }
  }
```

このコードではどんなurlのリクエストが飛んできてもhtmlディレクトリ以下の静的なファイルを探しにいくようになっており、APIリクエストをlaravelコンテナにリクエストを転送することができていなかった。よって次のように設定を書き直した。

```
    types {
    text/plain yaml;
    text/plain yml;
  }

  gzip on;
  gzip_static on;
  gzip_disable "msie6";

  gzip_vary on;
  gzip_types text/plain text/css application/javascript;

  map $request_method $access_control_max_age {
    OPTIONS 1728000; # 20 days
  }
  server_tokens off; # Hide Nginx version

  server {
    listen            8080;
    server_name       localhost;


    location / {
      absolute_redirect off;
      alias            /usr/share/nginx/html/;
      index index.html index.htm;

      expires 1d;

      location ~ swagger-initializer.js {
        expires -1;
        include templates/cors.conf;
      }

      location ~* \.(?:json|yml|yaml)$ {
        #SWAGGER_ROOT
        expires -1;

        include templates/cors.conf;
      }

      include templates/cors.conf;
      include templates/embedding.conf;
    }

    //追加
    location ~ ^/api/ {
        proxy_pass http://laravel.test;  # Laravelコンテナ名
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }
  }
```
このように/api/から始まるリクエストがきたらそれをlaravelコンテナに転送するように設定をした。またdefault.confを直接書き直すだけだとdockerを再起動したら元の設定が再作成されてしまったので、default.conf.templateの方を書き換えることでそれを防いだ。

### 基本課題③：Seederの実装
データをマニュアルで用意するのが面倒だったのでFactoryのfakerヘルパを用いてデータを作成した。

### 応用課題②：認証機能の実装
Sanctumを用いて認証機能を実装した。

## 取り組んだ課題ごとの質問、疑問
どの課題についても、この実装が実務上一般的でわかりやすいものなのかどうか、またそうでない部分があれば教えていただきたいです。それ以外にも変なコードやよくない実装があれば教えていただきたいです。
