# docker-laravel handson

## まえせつ

### Dockerとは

VMWareやVirtualBoxなど・・・<font color="red">物理マシン</font>を仮想化

Docker・・・<font color="red">OS</font>を仮想化（厳密には違うけどイメージ...）

-------

仮想マシンの場合、実際の物理的なコンピュータと同じようなものがソフトウェアのように振る舞っているのでOSが必要になる。

コンテナはホストOS上で直接起動する単なるプロセスのためOSは必要ない。

通常のプロセスと異なる点は、プロセスのかたまりをグループ化し独立した空間（コンテナ）で動作させる点。

![コンテナの概念図](https://knowledge.sakura.ad.jp/images/2018/01/VM_Container-680x387.jpg)

-------------

コンテナ上のアプリはホストOSの<font color="red">カーネル</font>という機能を呼び出して実行している。

![img](https://thinkit.co.jp/sites/default/files/article_node/bft_container_ojisan_02_05.jpg)

--------

### コンテナ（Docker）のメリット

- **リソースの無駄遣いが少ない**

  プロセス単位で独立したコンテナを作成するため、各コンテナに使用するリソースを分割、分配、制限することができる。

- **起動が速い**

  OSを起動しないので、ホストOSからは通常のプロセスが起動するのとほとんど差がない。

- **アプリケーションのみの起動が可能**

  仮想マシンと異なり、コンテナ内のアプリケーションを起動するのにOSや各種デーモンを起動する必要がない。Webサーバに必要なプロセスのみが起動しているといった環境を作ることができ、このような環境を<font color="red">アプリケーションコンテナ</font>と呼ぶ。

  通常のOSが起動するのと同じような環境（<font color="red">システムコンテナ</font>）も作成可能。

### コンテナ（Docker）のデメリット

- **Linux上でしか動作しない**

  DockerはLinuxカーネルを利用した技術のため、WindowsやMacなどをホストOSにすることはできない。

  →例えばWindows上にLinux仮想マシンを立て、その上にDocker環境を構築することは可能

- **異なるOSのシステム、プログラムは動かせない**

  ホストOS以外のOS用プログラムは動作しない。Linuxの任意のディストリビューションはインストールできるが、Ubuntu上にCentOSのディストリビューションをインストールしても動作しているカーネルはUbuntuになる。

- **コンテナ毎に異なったカーネルに関わる操作はできない**
  あくまで全コンテナから見えるカーネルは同一のため、それぞれのコンテナで異なるカーネル操作はできない。



## Dockerハンズオン

<font color="red">Docker環境が揃っている人は3までスキップ</font>

## 1.Docker Desktop for Windowsの入手

- [Docker Hub](https://hub.docker.com/)にサインイン

- Docker Desktop for Windowsをダウンロード



## 2.WSL環境の構築

- 以下の手順に沿って設定する（手順6まで）

https://docs.microsoft.com/ja-jp/windows/wsl/install-manual#step-4---download-the-linux-kernel-update-package

※今回はOSにUbuntu20.04 LTSを使用



## 3.Dockerを使ってみる

### 3-1.Docker HubからDocker imageを落とす（pull）

```bash
$ docker login
ユーザー情報を聞かれるのでDocker Hubに登録したDockerIDとパスワードを入力
Login Succeededと出ればOK

$ docker pull hello-world:latest
hello-worldというDockerimageをDockerHubのレジストリからpull

$ docker images
手元にあるDockerimageのリストを表示
```



### 3-2.Docker imageからコンテナを作成する（run）

```bash
$ docker run hello-world
hello-worldに組み込まれた、テキストを出力する命令が実行される

$ docker ps -a
コンテナのリストを表示　docker ps の場合はアクティブのコンテナのみ表示
StatusがExitedになっていることを確認
普通にrunするとコンテナを作成しても直ちにexitする
コンテナを出ずにコンテナに入るにはdocker run -it {IMAGE名:TAG名} {コマンド}
コンテナを作成した後に実行したいコマンドを書く

$ docker run -it ubuntu bash
ローカルにubuntuのDockerimageがないが、指定されたレジストリ（今回はDockerHub）から勝手にpullしてrunしてくれる
今回はDockerimageを構成するDockerimagelayerを4つpullし、コンテナ起動後にbashを起動
（デフォルトだとルート権限でbashが実行されるので注意が必要）
```



### 3-3.コンテナの更新

```bash
# pwd
カレントディレクトリはデフォルトではroot（Dockerimageで定義できる）

# ls
Ubuntuのルート配下のファイルが表示される

# touch test
# ls
testファイルを作成、確認

# exit
コンテナから出る

$ docker ps -a
StatusがExitedになっていることを確認
コンテナの名前をNAMES欄で確認
ubuntuはあくまでもimage名、コンテナ名の指定がなければランダムに命名される
名前の指定をしたいときはdocker run --name{コンテナ名} -it {IMAGE名:TAG名} {コマンド}

$ docker restart {コンテナ名}
$ docker ps -a
コンテナを再起動(restart)し、ステータスがUpになっていることを確認

$ docker exec -it {コンテナ名} bash
既に存在するコンテナに入るにはexecコマンド（runは作成するコマンド）

# ls
testファイルがあることを確認

コンテナを動かしたままコンテナから出たい場合はdetachをする
detachはコマンドではなく、ctrl+p+qのショートカットを実行する
detachするとプロセスが残り続けるので、再度execで入ってexitしてもExitedにならない
detachしたときのプロセスに戻るにはdocker attach {コンテナ名}を実行してexitする
```



### 3-4.コンテナをcommitしてDocker imageとして保存する

```bash
$ docker commit {コンテナ名} {新しいDockerimage名}
$ docker images
新しいDockerimageで保存した後、確認

$ docker run -it {新しいDockerimage名} bash
# ls
testファイルがあることを確認
```



### 3-5.Docker imageをDocker Hubにpushする

- [Docker Hub](https://hub.docker.com/)にアクセスし、Create Repositoryでリポジトリを作成する

```bash
$ docker tag {旧IMAGE名:旧TAG名} {新IMAGE名:新TAG名}
image名を元にpush先を決めるため、リポジトリ名=image名に変更しなければならない

$ docker push {IMAGE名(リポジトリ名)}
```



### 3-6.Dockerfileを作成しビルドする

```bash
$ mkdir docker
$ cd docker
$ notepad Dockerfile
```
作成したDockerfileに以下を記入
```text
FROM ubuntu:latest
RUN touch test
```

```bash
$ docker build .
$ docker images
DockerfileからDockerimageをビルドし確認

$ docker run -it {IMAGE ID} bash
今回はIMAGE名がないのでIDでrun

# ls
testファイルがあることを確認
```

**コンテナを更新する方法は「コンテナを直接更新」、「Dockerfileを更新」する二つの方法があるが基本的には<font color = "red">「Dockerfileを更新」</font>するようにする**

https://docs.microsoft.com/en-us/sql/connect/odbc/linux-mac/installing-the-microsoft-odbc-driver-for-sql-server?view=sql-server-ver15#ubuntu17



## 4.Docker+Git+Laravel+NginX+Mysql

### 4-1.Git初期設定

```bash
gitユーザー名、メールアドレスの登録
$ git config --global user.name "おーた"
$ git config --global user.email "ota.yu@mic-p.com"

ディレクトリ、ファイル作成、権限設定
$ mkdir ~/.ssh
$ touch ~/.ssh/config
$ chmod 700 ~/.ssh
$ chmod 600 ~/.ssh/*
```

```txt
~/.ssh/config

Host *
  StrictHostKeyChecking no
  UserKnownHostsFile=/dev/null
  ServerAliveInterval 15
  ServerAliveCountMax 30
  IdentitiesOnly yes
```

参考

https://qiita.com/ucan-lab/items/aadbedcacbc2ac86a2b3



### 4-2.GitSSH設定

```bash
GitHub用の秘密鍵、公開鍵を作成
$ ssh-keygen -t ed25519 -N "" -f ~/.ssh/github
```

- ~/.ssh/github.pubの内容を全てコピーし、GitHubの公開鍵設定画面を開く

https://github.com/settings/keys

- `New SSH Key`

- Title を適当に入力する(PC名を入れておくと鍵管理しやすい)
- Key にクリップボードにコピーした公開鍵を貼り付ける
- `Add SSH Key` で鍵を登録する

```txt
~/.ssh/configに追記

Host github.com
  IdentityFile ~/.ssh/github
  User git
```

```bash
$ ssh -T github.com
successfullyと出ればOK（Warningは無視）
```

参考

https://qiita.com/ucan-lab/items/e02f2d3a35f266631f24#_reference-7cbea668d512073f0df4

※参考見てSSH繋がらない人↓

https://gotohayato.com/content/466/



```
$ git clone git@github.com:mic-yyg/laravel-docker.git

```



※docker compose upできない人↓

https://qiita.com/iwaiktos/items/33ab69a42c3a1cc35dfb