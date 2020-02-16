# laravel_login_and_crawler

登入介面與爬蟲

git pull 後請於server端該下載目錄執行以下指令

1.
安裝vendor
```
composer update
```
2.
建立2張資料表
用戶表 users
星座資訊表 astros
```
php artisan migrate
```
3.
在crontab中加入以下指令
```
*  *    * * *   root     php /var/www/LaravelLogin/artisan schedule:run >> /dev/null 2>&1
```
若原來的crontab格式中不需要填入user 則使用以下指令
```
*  *    * * *   php /var/www/LaravelLogin/artisan schedule:run >> /dev/null 2>&1
```

4.
重啟crontab(ubuntu為例)
```
service cron reload
service cron restart
```





