<?php 
include 'db.php'; 

// Pagination Logic (20 Lines)
function paginateNote($text, $limit = 20) {
    $lines = explode("\n", $text);
    $pages = []; $currentPage = []; $lineCount = 0;
    foreach ($lines as $line) {
        $estimatedLines = ceil(mb_strlen($line) / 50) ?: 1;
        if ($lineCount + $estimatedLines > $limit) {
            $pages[] = $currentPage; $currentPage = []; $lineCount = 0;
        }
        $currentPage[] = $line; $lineCount += $estimatedLines;
    }
    if (!empty($currentPage)) $pages[] = $currentPage;
    return $pages;
}

function formatLine($line) {
    $line = trim($line);
    if (empty($line)) return "<div class='line-row'>&nbsp;</div>";
    if (strpos($line, '#') === 0) return "<div class='line-row h-red'>".ltrim($line, '# ')."</div>";
    if (strpos($line, '*') === 0) return "<div class='line-row m-blue'>• ".ltrim($line, '* ')."</div>";
    if (strpos($line, '-') === 0) return "<div class='line-row s-black'>➥ ".ltrim($line, '- ')."</div>";
    return "<div class='line-row'>".$line."</div>";
}

$sql = "SELECT * FROM question_notes ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="si">
<head>
    <meta charset="UTF-8">
    <title>Master 3D Study System</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&family=Patrick+Hand&display=swap" rel="stylesheet">
    <style>
        :root { --paper: #fffcf2; --ink-red: #ff0000; --ink-blue: #0000ff; --line: #d1d8e0; }
        body { background: #1a1a1a; height: 100vh; display: flex; flex-direction: column; justify-content: center; align-items: center; perspective: 2500px; margin: 0; overflow: hidden; font-family: 'Noto Sans Sinhala', sans-serif; }
        
        /* Toolbar */
        .toolbar { position: absolute; top: 15px; display: flex; gap: 15px; z-index: 2000; background: rgba(0,0,0,0.5); padding: 10px; border-radius: 50px; }
        .color-dot { width: 25px; height: 25px; border-radius: 50%; cursor: pointer; border: 2px solid white; }
        
        /* Book & Pages */
        .viewport { transform: scale(0.85) rotateX(10deg); transform-style: preserve-3d; }
        .book { position: relative; width: 480px; height: 650px; transform-style: preserve-3d; }
        .paper { position: absolute; width: 100%; height: 100%; transform-origin: left; transition: 0.8s; transform-style: preserve-3d; cursor: pointer; }
        .front, .back { position: absolute; width: 100%; height: 100%; background: var(--paper); backface-visibility: hidden; padding: 40px 30px 30px 50px; border-radius: 0 10px 10px 0; background-image: linear-gradient(var(--line) 1px, transparent 1px); background-size: 100% 30px; }
        .back { transform: rotateY(180deg); border-radius: 10px 0 0 10px; background: #fff; }
        .front::before { content: ''; position: absolute; left: 42px; top: 0; width: 1.5px; height: 100%; background: rgba(255,0,0,0.2); }
        
        /* Text styles */
        .line-row { min-height: 30px; line-height: 30px; font-size: 1.05rem; word-wrap: break-word; color: #333; }
        .h-red { color: var(--ink-red); font-weight: bold; text-decoration: underline; font-family: 'Patrick Hand'; }
        .m-blue { color: var(--ink-blue); font-weight: bold; }
        .s-black { color: #000; padding-left: 20px; font-style: italic; }

        /* Cover Colors */
        .cover { display: flex; align-items: center; justify-content: center; color: gold; border-left: 15px solid rgba(0,0,0,0.3); }
        .bg-brown { background: #4b2c20 !important; }
        .bg-blue { background: #1e3799 !important; }
        .bg-green { background: #1b1464 !important; }
        .bg-red { background: #b71540 !important; }

        .flipped { transform: rotateY(-175deg); }
        
        /* Navigation & Audio Icons */
        .page-tools { position: absolute; bottom: 15px; right: 20px; display: flex; gap: 15px; z-index: 100; }
        .icon-btn { cursor: pointer; font-size: 1.2rem; opacity: 0.5; transition: 0.3s; }
        .icon-btn:hover { opacity: 1; color: var(--ink-blue); }
        .delete-btn:hover { color: red; }

        /* Slider */
        .nav-slider { position: absolute; bottom: 30px; width: 300px; z-index: 2000; }
    </style>
</head>
<body>

<div class="toolbar">
    <div class="color-dot bg-brown" onclick="changeCover('bg-brown')"></div>
    <div class="color-dot bg-blue" onclick="changeCover('bg-blue')"></div>
    <div class="color-dot bg-green" onclick="changeCover('bg-green')"></div>
    <div class="color-dot bg-red" onclick="changeCover('bg-red')"></div>
</div>

<div class="viewport">
    <div class="book" id="mainBook">
        <div class="paper" style="z-index: 1000;">
            <div class="front cover bg-brown" id="bookCover">
                <h1 style="font-family:'Patrick Hand'; font-size: 3.5rem;">STUDY BOOK</h1>
            </div>
            <div class="back"><h2>Index</h2><p>Use slider to jump to pages.</p></div>
        </div>

        <?php 
        $pCount = 1;
        while($row = $result->fetch_assoc()): 
            $pages = paginateNote($row['note'], 20);
            foreach($pages as $idx => $content):
                $textToRead = str_replace(["#", "*", "-"], "", implode(" ", $content));
        ?>
            <div class="paper" id="p-<?php echo $pCount++; ?>">
                <div class="front">
                    <div id="text-<?php echo $pCount; ?>">
                        <?php foreach($content as $line) echo formatLine($line); ?>
                    </div>
                    <div class="page-tools">
                        <span class="icon-btn" onclick="readAloud(`<?php echo addslashes($textToRead); ?>`)">🔊</span>
                        <span class="icon-btn delete-btn" onclick="deleteNote(<?php echo $row['id']; ?>)">🗑️</span>
                    </div>
                </div>
                <div class="back">
                    <?php if($idx == 0 && !empty($row['image_path'])): ?>
                        <img src="<?php echo $row['image_path']; ?>" style="width:100%; border:5px solid #fff;">
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; endwhile; ?>
    </div>
</div>

<input type="range" min="0" max="<?php echo $pCount; ?>" value="0" class="nav-slider" id="pageSlider">

<script>
    const papers = document.querySelectorAll('.paper');
    const slider = document.getElementById('pageSlider');

    // Page Z-Index & Click
    papers.forEach((p, i) => {
        p.style.zIndex = papers.length - i;
        p.onclick = (e) => {
            if(e.target.classList.contains('icon-btn')) return;
            p.classList.toggle('flipped');
            updateZIndex();
        };
    });

    function updateZIndex() {
        papers.forEach((p, i) => {
            if (p.classList.contains('flipped')) p.style.zIndex = i + 1;
            else p.style.zIndex = papers.length - i;
        });
    }

    // Feature 4: Audio Reader
    function readAloud(text) {
        window.speechSynthesis.cancel();
        const msg = new SpeechSynthesisUtterance(text);
        msg.lang = 'en-US'; // සිංහල support එක browser එක මත රඳා පවතී
        window.speechSynthesis.speak(msg);
    }

    // Feature 10: Cover Gallery
    function changeCover(cls) {
        const cover = document.getElementById('bookCover');
        cover.className = "front cover " + cls;
    }

    // Feature 12: Slider Navigation
    slider.oninput = function() {
        const val = parseInt(this.value);
        papers.forEach((p, i) => {
            if (i < val) p.classList.add('flipped');
            else p.classList.remove('flipped');
        });
        updateZIndex();
    };

    // Note Delete
    function deleteNote(id) {
        if(confirm("මම මේ Note එක මකන්නද?")) {
            window.location.href = "delete.php?id=" + id;
        }
    }
</script>
</body>
</html>