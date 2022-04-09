<?php require '../HEAD/header.php';?>
<link rel="stylesheet" href="syoi.css">
</head>
<body>
<label class="sort">
    <select  name="search_sort" class="sort-design" onChange="location.href=value;">
        <option value="syoi-sort.php?value=1">名前(あ～A)</option>
        <option value="syoi-sort.php?value=2">名前(ん～Z)</option>
        <option value="syoi-sort.php?value=3">発売日(新しい)</option>
        <option value="syoi-sort.php?value=4">発売日(古い)</option>
        <option value="syoi-sort.php?value=5">価格(安い)</option>
        <option value="syoi-sort.php?value=6">価格(高い)</option>
    </select>
</label>

<?php require 'pdo.php';

//タブがクリックされた場合代入させる変数
$tab='';

//検索結果を代入する変数、ワードやタブの結果を格納する
$result='';

//検索ボックスに入った値を取り出す変数
$searchword='';

//検索ボックス横のドロップボックスの値を取得
$hardnumber='';

//もし検索ボックスに値が入っていた場合searchwordに値を代入
if(!empty($_GET['search'])){
    $searchword=$_GET['search'];
}
if(!empty($_GET['hard_number'])){
    $hardnumber=$_GET['hard_number'];
}
//検索ワードを取得し値がなかった場合はタブの判定.全てに当てはまらなかった場合全件のqueryを出力させる
//クリック:ジャンルタブ
if(!empty($_GET['genre_id'])){
    $result=genresearch($_GET['genre_id']);

//クリック:ハードタブ
}else if(!empty($_GET['hard_id'])){
    $result=hardsearch($_GET['hard_id']);

//クリック:popularタブ
}else if(!empty($_GET['other_id'])){
    $result=othersearch($_GET['other_id']);
}
else if(!empty($_GET['search'])){
    $searchword = $_GET['search'];
    $result = search($searchword,$hardnumber);
} else {
    $word='';
    $result = search($word,$hardnumber);
}


?>
<div class="search-result">
    <?php
    if(!empty($_GET['search'])){
        $word= htmlspecialchars($_GET['search']);
        echo $word,'の検索結果';
    }else {
        echo '検索結果';
    }
    ?>
</div>
<!-- 検索結果が存在していた場合 -->
<div id="column" class="column04">
    <ul>
<?php if($result != false ){
foreach($result as  $item){
?>
            <li>
                <a href="../SYOS/syos.php?goods_id=<?php echo $item['goods_id'] ;?>"><img src="../IMG/goods/<?=$item['image_name']?>" alt="商品画像" onclick=location.href="../SYOS/syos.php?goods_id=<?=$item['goods_id']?> ID="goods-img">
                    <p>
                        <div class="">
                            <a href="../SYOS/syos.php?goods_id=<?php echo $item['goods_id'] ;?>"><?php echo $item['game_name'] ;?></a>
                        </div>
                    </p>
                    <span><?php echo $item['goods_price'];?>円</span>
                </a>
            </li>
<?php
}
    }else{ ?>
        <div id="UNSEARCH">
            <div class="unsearch-word">該当する検索結果がありませんでした</div>
            <p>他のキーワードを入力してください</p>
        </div>
    <?php } ?>
    </ul>
</body>
</html>