<?php
//DB接続用関数 説明のためpdoのdns,username,passwordは潰しました
function pdo(){
        $pdo = new PDO('mysql:host=mysql154.phy.lolipop.lan;dbname=DBname;charset=utf8','---','***');
        return $pdo;
}

//検索ワードを引数としてDBから対象の商品一覧を表示させる
function search($word,$hardnumber){
    $pdo = pdo();
    $search_result=[];
    //ドロップボックスがall以外の場合
    if($hardnumber >0){
        //検索ワードが何も入っていなかった場合
        if(!isset($word)){
            $sql = $pdo ->prepare('
                    select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
                    from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
                    where and ha.hard_id = ? and go.goods_delete=0');
                $sql -> bindValue(1,$hardnumber);
            //戻り地が一個でもあった場合
        }else{
            $sql = $pdo ->prepare('
            select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
            from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
            where game_name like ? and ha.hard_id = ? and go.goods_delete=0');
            $sql -> bindValue(1,'%'.htmlspecialchars($word).'%',PDO::PARAM_STR);
            $sql -> bindValue(2,$hardnumber);
        }
        //検索ドロップボックスにALLが指定されていた場合
    }else{ 
        //検索ワードが何も入っていなかった場合
        if(!isset($word)){
            $sql = $pdo ->prepare('
                    select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
                    from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
                    where go.goods_delete=0');
                $sql -> bindValue(1,$hardnumber);
            //戻り地が一個でもあった場合
        }else {
            //検索ワードが入っていた場合
            $sql = $pdo ->prepare('
            select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
            from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
            where game_name like ? and go.goods_delete=0');
            $sql -> bindValue(1,'%'.htmlspecialchars($word).'%',PDO::PARAM_STR);
            //戻り地が一個でもあった場合
        }
    }
    $sql -> execute();
    $result = $sql -> fetchAll();
    if($sql -> rowCount()>0){
        $search_result = $result;
    }else{
        $search_result= false;
    }
    return $search_result;
}


//タグから飛んできた場合コチラの関数が動くようにする。
function genresearch($genre){
    $pdo = pdo();
    $search_result=[];

    //タグの値のよって表示させる商品を変更させる
    //ジャンル
    if(!empty($genre)){
        $sql = $pdo ->prepare('select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
        from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
        where ge.genre_id=? and go.goods_delete=0');
        $sql -> bindValue(1,$genre);
        $sql -> execute();
        $query_result = $sql -> fetchAll(PDO::FETCH_ASSOC);
        if(!empty($query_result)){
            $search_result = $query_result;
        }else{
            $search_result =false;
        }
    }
    return $search_result;
}

function hardsearch($hard){
    $pdo = pdo();
    $search_result=[];

    //タグの値のよって表示させる商品を変更させる
    //ハード
    if(!empty($hard)){
        $sql = $pdo ->prepare('select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
        from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
        where go.hard_id=? and go.goods_delete=0');
        $sql -> bindValue(1,$hard);
        $sql -> execute();
        $query_result = $sql -> fetchAll(PDO::FETCH_ASSOC);
        if(!empty($query_result)){
            $search_result = $query_result;
        }else{
            $search_result =false;
        }
    }
    return $search_result;
}

function othersearch($other){
    $pdo = pdo();
    $search_result=[];

    //タグの値のよって表示させる商品を変更させる
    //人気度,クロスプレイ
    //もし人気度の項目がクリックされたら
    if(!empty($other) && $other==1){
        $sql = $pdo ->prepare('select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name,po.goods_popular
            from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id join popular po on go.popular_id=po.popular_id
            where go.goods_delete=0 order by po.popular_id DESC');
        $sql -> execute();
        $query_result = $sql -> fetchAll(PDO::FETCH_ASSOC);
        if(!empty($query_result)){
            $search_result = $query_result;
        }else{
            $search_result =false;
        }
        //クロスプレイで検索をかけた場合
    }else if(!empty($other) && $other==2){
        $sql = $pdo ->prepare('select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,goods_releasedate,im.image_name
            from (((goods go join game ga on go.game_id = ga.game_id) join genre ge on ga.genre_id = ge.genre_id) join hard ha on go.hard_id = ha.hard_id) join gameimage im on ga.image_id=im.image_id
            where crossplay_flag=1 and go.goods_delete=0');
        $sql -> execute();
        $query_result = $sql -> fetchAll(PDO::FETCH_ASSOC);
        if(!empty($query_result)){
            $search_result = $query_result;
        }else{
            $search_result =false;
        }
    }
    return $search_result;
}


//商品並べ替え(常時対象:全件)
function sortgoods($value){
    $pdo = pdo();
    $result='';
    $sql='';
    switch ($value){
        case 1:
            //全件検索
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by ga.game_name');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        case 2:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by ga.game_name desc');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        case 3:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by goods_releasedate');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        case 4:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by goods_releasedate desc');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        case 5:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by goods_price');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        case 6:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0 order by goods_price desc');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
        default:
            $sql = $pdo ->prepare('
                select go.goods_id,ga.game_name,ge.genre_name,ha.hard_name,go.goods_price,ga.crossplay_flag,go.goods_download,ga.goods_description,goods_releasedate,im.image_name
                from goods go join game ga on go.game_id = ga.game_id join genre ge on ga.genre_id = ge.genre_id join hard ha on go.hard_id = ha.hard_id join gameimage im on ga.image_id=im.image_id 
                where go.goods_delete=0');
            $sql -> execute();
            $result = $sql -> fetchAll(PDO::FETCH_ASSOC);
            break;
    }
    return $result;
}



?>