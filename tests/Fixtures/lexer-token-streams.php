<?php

return [
    'performance/tests/dropify/article.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div class="article">
  <h2 class="article-title">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>
  <p class="article-details">posted <span class="article-time">',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 3,
        ],
        11 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 3,
        ],
        13 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        14 => [
            0 => 'String',
            1 => '"%Y %h"',
            2 => 3,
        ],
        15 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        16 => [
            0 => 'TextData',
            1 => '</span> by <span class="article-author">',
            2 => 3,
        ],
        17 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        19 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 3,
        ],
        21 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        22 => [
            0 => 'TextData',
            1 => '</span></p>

  <div class="article-body textile">
    ',
            2 => 3,
        ],
        23 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        25 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 6,
        ],
        27 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        28 => [
            0 => 'TextData',
            1 => '
  </div>

</div>

<!-- Comments -->
',
            2 => 6,
        ],
        29 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 12,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 12,
        ],
        32 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 12,
        ],
        34 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        35 => [
            0 => 'TextData',
            1 => '
<div id="comments">
  <h3>Comments</h3>

  <!-- List all comments -->
  <ul id="comment-list">
  ',
            2 => 12,
        ],
        36 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 18,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 18,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 18,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 18,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'comments',
            2 => 18,
        ],
        43 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        44 => [
            0 => 'TextData',
            1 => '
    <li>
      <div class="comment-details">
        <span class="comment-author">',
            2 => 18,
        ],
        45 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 21,
        ],
        47 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 21,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        50 => [
            0 => 'TextData',
            1 => '</span> said on <span class="comment-date">',
            2 => 21,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 21,
        ],
        53 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 21,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 21,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 21,
        ],
        57 => [
            0 => 'Colon',
            1 => ':',
            2 => 21,
        ],
        58 => [
            0 => 'String',
            1 => '"%B %d, %Y"',
            2 => 21,
        ],
        59 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        60 => [
            0 => 'TextData',
            1 => '</span>:
      </div>

      <div class="comment">
        ',
            2 => 21,
        ],
        61 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 25,
        ],
        63 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 25,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        66 => [
            0 => 'TextData',
            1 => '
      </div>
    </li>
  ',
            2 => 25,
        ],
        67 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 28,
        ],
        69 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        70 => [
            0 => 'TextData',
            1 => '
  </ul>

  <!-- Comment Form -->
  <div id="comment-form">
  ',
            2 => 28,
        ],
        71 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 33,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 33,
        ],
        74 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        75 => [
            0 => 'TextData',
            1 => '
    <h3>Leave a comment</h3>

    <!-- Check if a comment has been submitted in the last request, and if yes display an appropriate message -->
    ',
            2 => 33,
        ],
        76 => [
            0 => 'BlockStart',
            1 => '',
            2 => 37,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 37,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 37,
        ],
        79 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'posted_successfully?',
            2 => 37,
        ],
        81 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 37,
        ],
        82 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 37,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 38,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 38,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 38,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        89 => [
            0 => 'TextData',
            1 => '
        <div class="notice">
          Successfully posted your comment.<br />
          It will have to be approved by the blog owner first before showing up.
        </div>
      ',
            2 => 38,
        ],
        90 => [
            0 => 'BlockStart',
            1 => '',
            2 => 43,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 43,
        ],
        92 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 43,
        ],
        93 => [
            0 => 'TextData',
            1 => '
        <div class="notice">Successfully posted your comment.</div>
      ',
            2 => 43,
        ],
        94 => [
            0 => 'BlockStart',
            1 => '',
            2 => 45,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 45,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 45,
        ],
        97 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 45,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 46,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        101 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 46,
        ],
        102 => [
            0 => 'BlockStart',
            1 => '',
            2 => 48,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 48,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 48,
        ],
        105 => [
            0 => 'Dot',
            1 => '.',
            2 => 48,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 48,
        ],
        107 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 48,
        ],
        108 => [
            0 => 'TextData',
            1 => '
      <div class="notice error">Not all the fields have been filled out correctly!</div>
    ',
            2 => 48,
        ],
        109 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 50,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        112 => [
            0 => 'TextData',
            1 => '

    <dl>
      <dt class="',
            2 => 50,
        ],
        113 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 53,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 53,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 53,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 53,
        ],
        118 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 53,
        ],
        119 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 53,
        ],
        120 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        121 => [
            0 => 'TextData',
            1 => 'error',
            2 => 53,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 53,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        125 => [
            0 => 'TextData',
            1 => '"><label for="comment_author">Your name</label></dt>
      <dd><input type="text" id="comment_author" name="comment[author]" size="40" value="',
            2 => 53,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 54,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 54,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 54,
        ],
        130 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 54,
        ],
        131 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 54,
        ],
        132 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 54,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 54,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 54,
        ],
        137 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 54,
        ],
        138 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 54,
        ],
        139 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        140 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 54,
        ],
        141 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 54,
        ],
        143 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        144 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 54,
        ],
        145 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 56,
        ],
        148 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 56,
        ],
        150 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 56,
        ],
        151 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 56,
        ],
        152 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        153 => [
            0 => 'TextData',
            1 => 'error',
            2 => 56,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        157 => [
            0 => 'TextData',
            1 => '"><label for="comment_email">Your email</label></dt>
      <dd><input type="text" id="comment_email" name="comment[email]" size="40" value="',
            2 => 56,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 57,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 57,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 57,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'email',
            2 => 57,
        ],
        162 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 57,
        ],
        163 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 57,
        ],
        164 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 57,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 57,
        ],
        167 => [
            0 => 'Dot',
            1 => '.',
            2 => 57,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 57,
        ],
        169 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 57,
        ],
        170 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 57,
        ],
        171 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        172 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 57,
        ],
        173 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 57,
        ],
        175 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        176 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 57,
        ],
        177 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        178 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 59,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 59,
        ],
        180 => [
            0 => 'Dot',
            1 => '.',
            2 => 59,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 59,
        ],
        182 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 59,
        ],
        183 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 59,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        185 => [
            0 => 'TextData',
            1 => 'error',
            2 => 59,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 59,
        ],
        188 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        189 => [
            0 => 'TextData',
            1 => '"><label for="comment_body">Your comment</label></dt>
      <dd><textarea id="comment_body" name="comment[body]" cols="40" rows="5" class="',
            2 => 59,
        ],
        190 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 60,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 60,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 60,
        ],
        195 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 60,
        ],
        196 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 60,
        ],
        197 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        198 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 60,
        ],
        199 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 60,
        ],
        201 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        202 => [
            0 => 'TextData',
            1 => '">',
            2 => 60,
        ],
        203 => [
            0 => 'VariableStart',
            1 => '',
            2 => 60,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 60,
        ],
        205 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'body',
            2 => 60,
        ],
        207 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 60,
        ],
        208 => [
            0 => 'TextData',
            1 => '</textarea></dd>
    </dl>

    ',
            2 => 60,
        ],
        209 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        210 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 63,
        ],
        211 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 63,
        ],
        212 => [
            0 => 'Dot',
            1 => '.',
            2 => 63,
        ],
        213 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 63,
        ],
        214 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        215 => [
            0 => 'TextData',
            1 => '
      <p class="hint">comments have to be approved before showing up</p>
    ',
            2 => 63,
        ],
        216 => [
            0 => 'BlockStart',
            1 => '',
            2 => 65,
        ],
        217 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 65,
        ],
        218 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 65,
        ],
        219 => [
            0 => 'TextData',
            1 => '

    <input type="submit" value="Post comment" id="comment-submit" />
  ',
            2 => 65,
        ],
        220 => [
            0 => 'BlockStart',
            1 => '',
            2 => 68,
        ],
        221 => [
            0 => 'Identifier',
            1 => 'endform',
            2 => 68,
        ],
        222 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 68,
        ],
        223 => [
            0 => 'TextData',
            1 => '
  </div>
  <!-- END Comment Form -->

</div>
',
            2 => 68,
        ],
        224 => [
            0 => 'BlockStart',
            1 => '',
            2 => 73,
        ],
        225 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 73,
        ],
        226 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 73,
        ],
        227 => [
            0 => 'TextData',
            1 => '
<!-- END Comments -->
',
            2 => 73,
        ],
    ],
    'performance/tests/dropify/blog.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="page">
  <h2>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>

  ',
            2 => 2,
        ],
        7 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 4,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 4,
        ],
        10 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 4,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 4,
        ],
        13 => [
            0 => 'Number',
            1 => '20',
            2 => 4,
        ],
        14 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        15 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 4,
        ],
        16 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 6,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 6,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 6,
        ],
        21 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 6,
        ],
        23 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        24 => [
            0 => 'TextData',
            1 => '

    <div class="article">
      <div class="headline">
      <h3 class="title">
        <a href="',
            2 => 6,
        ],
        25 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 11,
        ],
        27 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 11,
        ],
        29 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        30 => [
            0 => 'TextData',
            1 => '">',
            2 => 11,
        ],
        31 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 11,
        ],
        33 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 11,
        ],
        35 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        36 => [
            0 => 'TextData',
            1 => '</a>
      </h3>
      <h4 class="date">Posted on ',
            2 => 11,
        ],
        37 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 13,
        ],
        39 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 13,
        ],
        41 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 13,
        ],
        43 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        44 => [
            0 => 'String',
            1 => '"%B %d, \'%y"',
            2 => 13,
        ],
        45 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        46 => [
            0 => 'TextData',
            1 => ' by ',
            2 => 13,
        ],
        47 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 13,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 13,
        ],
        51 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        52 => [
            0 => 'TextData',
            1 => '.</h4>
      </div>

      <div class="article-body textile">
        ',
            2 => 13,
        ],
        53 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 17,
        ],
        55 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 17,
        ],
        57 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 17,
        ],
        59 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 17,
        ],
        61 => [
            0 => 'Colon',
            1 => ':',
            2 => 17,
        ],
        62 => [
            0 => 'Number',
            1 => '250',
            2 => 17,
        ],
        63 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        64 => [
            0 => 'TextData',
            1 => '
      </div>

      ',
            2 => 17,
        ],
        65 => [
            0 => 'BlockStart',
            1 => '',
            2 => 20,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 20,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 20,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 20,
        ],
        70 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 20,
        ],
        71 => [
            0 => 'TextData',
            1 => '
        <p style="text-align: right"><a href="',
            2 => 20,
        ],
        72 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 21,
        ],
        74 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 21,
        ],
        76 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        77 => [
            0 => 'TextData',
            1 => '#comments">',
            2 => 21,
        ],
        78 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 21,
        ],
        80 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'comments_count',
            2 => 21,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        83 => [
            0 => 'TextData',
            1 => ' comments</a></p>
      ',
            2 => 21,
        ],
        84 => [
            0 => 'BlockStart',
            1 => '',
            2 => 22,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 22,
        ],
        86 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 22,
        ],
        87 => [
            0 => 'TextData',
            1 => '
    </div>

    ',
            2 => 22,
        ],
        88 => [
            0 => 'BlockStart',
            1 => '',
            2 => 25,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 25,
        ],
        90 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 25,
        ],
        91 => [
            0 => 'TextData',
            1 => '

    <div id="pagination">
      ',
            2 => 25,
        ],
        92 => [
            0 => 'VariableStart',
            1 => '',
            2 => 28,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 28,
        ],
        94 => [
            0 => 'Pipe',
            1 => '|',
            2 => 28,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 28,
        ],
        96 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 28,
        ],
        97 => [
            0 => 'TextData',
            1 => '
    </div>

  ',
            2 => 28,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 31,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 31,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 31,
        ],
        101 => [
            0 => 'TextData',
            1 => '

</div>
',
            2 => 31,
        ],
    ],
    'performance/tests/dropify/cart.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<script type="text/javascript">
  function remove_item(id) {
      document.getElementById(\'updates_\'+id).value = 0;
      document.getElementById(\'cartform\').submit();
  }
</script>

<div>

  ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 10,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 10,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 10,
        ],
        6 => [
            0 => 'Comparison',
            1 => '==',
            2 => 10,
        ],
        7 => [
            0 => 'Number',
            1 => '0',
            2 => 10,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        9 => [
            0 => 'TextData',
            1 => '
    <h4>Your shopping cart is looking rather empty...</h4>
  ',
            2 => 10,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 12,
        ],
        12 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        13 => [
            0 => 'TextData',
            1 => '
  <form action="/cart" method="post" id="cartform">

  <div id="cart">

  <h3>You have ',
            2 => 12,
        ],
        14 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 17,
        ],
        16 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 17,
        ],
        18 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        19 => [
            0 => 'TextData',
            1 => ' ',
            2 => 17,
        ],
        20 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 17,
        ],
        22 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 17,
        ],
        24 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 17,
        ],
        26 => [
            0 => 'Colon',
            1 => ':',
            2 => 17,
        ],
        27 => [
            0 => 'String',
            1 => '\'product\'',
            2 => 17,
        ],
        28 => [
            0 => 'Comma',
            1 => ',',
            2 => 17,
        ],
        29 => [
            0 => 'String',
            1 => '\'products\'',
            2 => 17,
        ],
        30 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        31 => [
            0 => 'TextData',
            1 => ' in here!</h3>

    <ul id="line-items">
      ',
            2 => 17,
        ],
        32 => [
            0 => 'BlockStart',
            1 => '',
            2 => 20,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 20,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 20,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 20,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 20,
        ],
        37 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'items',
            2 => 20,
        ],
        39 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 20,
        ],
        40 => [
            0 => 'TextData',
            1 => '
      <li id="item-',
            2 => 20,
        ],
        41 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 21,
        ],
        43 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 21,
        ],
        45 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        46 => [
            0 => 'TextData',
            1 => '" class="clearfix">
        <div class="thumb">
          <div class="prodimage">
          <a href="',
            2 => 21,
        ],
        47 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 24,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        51 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 24,
        ],
        53 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        54 => [
            0 => 'TextData',
            1 => '" title="View ',
            2 => 24,
        ],
        55 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 24,
        ],
        57 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 24,
        ],
        59 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        60 => [
            0 => 'TextData',
            1 => ' Page"><img src="',
            2 => 24,
        ],
        61 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 24,
        ],
        63 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        65 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 24,
        ],
        67 => [
            0 => 'Pipe',
            1 => '|',
            2 => 24,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 24,
        ],
        69 => [
            0 => 'Colon',
            1 => ':',
            2 => 24,
        ],
        70 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 24,
        ],
        71 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        72 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 24,
        ],
        73 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 24,
        ],
        75 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 24,
        ],
        77 => [
            0 => 'Pipe',
            1 => '|',
            2 => 24,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 24,
        ],
        79 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        80 => [
            0 => 'TextData',
            1 => '" /></a>
        </div></div>
        <h3 style="padding-right: 150px">
      <a href="',
            2 => 24,
        ],
        81 => [
            0 => 'VariableStart',
            1 => '',
            2 => 27,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 27,
        ],
        83 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 27,
        ],
        85 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 27,
        ],
        87 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 27,
        ],
        88 => [
            0 => 'TextData',
            1 => '" title="View ',
            2 => 27,
        ],
        89 => [
            0 => 'VariableStart',
            1 => '',
            2 => 27,
        ],
        90 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 27,
        ],
        91 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 27,
        ],
        93 => [
            0 => 'Pipe',
            1 => '|',
            2 => 27,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 27,
        ],
        95 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 27,
        ],
        96 => [
            0 => 'TextData',
            1 => ' Page">
        ',
            2 => 27,
        ],
        97 => [
            0 => 'VariableStart',
            1 => '',
            2 => 28,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 28,
        ],
        99 => [
            0 => 'Dot',
            1 => '.',
            2 => 28,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 28,
        ],
        101 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 28,
        ],
        102 => [
            0 => 'TextData',
            1 => '
        ',
            2 => 28,
        ],
        103 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 29,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 29,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        108 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'available',
            2 => 29,
        ],
        110 => [
            0 => 'Comparison',
            1 => '==',
            2 => 29,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'true',
            2 => 29,
        ],
        112 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        113 => [
            0 => 'TextData',
            1 => '
           (',
            2 => 29,
        ],
        114 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 30,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 30,
        ],
        118 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 30,
        ],
        120 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        121 => [
            0 => 'TextData',
            1 => ')
        ',
            2 => 30,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 31,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 31,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 31,
        ],
        125 => [
            0 => 'TextData',
            1 => '
      </a>
    </h3>
        <small class="itemcost">Costs ',
            2 => 31,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 34,
        ],
        130 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 34,
        ],
        132 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        133 => [
            0 => 'TextData',
            1 => ' each, <span class="money">',
            2 => 34,
        ],
        134 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        136 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'line_price',
            2 => 34,
        ],
        138 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 34,
        ],
        140 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        141 => [
            0 => 'TextData',
            1 => '</span> total.</small>
        <p class="right">
          <label for="updates">How many? </label>
          <input type="text" size="4" name="updates[',
            2 => 34,
        ],
        142 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 37,
        ],
        144 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 37,
        ],
        146 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 37,
        ],
        148 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        149 => [
            0 => 'TextData',
            1 => ']" id="updates_',
            2 => 37,
        ],
        150 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 37,
        ],
        152 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 37,
        ],
        154 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 37,
        ],
        156 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        157 => [
            0 => 'TextData',
            1 => '" value="',
            2 => 37,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 37,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'quantity',
            2 => 37,
        ],
        162 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        163 => [
            0 => 'TextData',
            1 => '" onfocus="this.select();"/><br />
          <a href="#" onclick="remove_item(',
            2 => 37,
        ],
        164 => [
            0 => 'VariableStart',
            1 => '',
            2 => 38,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 38,
        ],
        166 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 38,
        ],
        168 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 38,
        ],
        170 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 38,
        ],
        171 => [
            0 => 'TextData',
            1 => '); return false;" class="remove"><img style="padding:15px 0 0 0;margin:0;" src="',
            2 => 38,
        ],
        172 => [
            0 => 'VariableStart',
            1 => '',
            2 => 38,
        ],
        173 => [
            0 => 'String',
            1 => '\'delete.gif\'',
            2 => 38,
        ],
        174 => [
            0 => 'Pipe',
            1 => '|',
            2 => 38,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 38,
        ],
        176 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 38,
        ],
        177 => [
            0 => 'TextData',
            1 => '" /></a>
        </p>
      </li>
      ',
            2 => 38,
        ],
        178 => [
            0 => 'BlockStart',
            1 => '',
            2 => 41,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 41,
        ],
        180 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 41,
        ],
        181 => [
            0 => 'TextData',
            1 => '
      <li id="total">
        <input type="image" id="update-cart" name="update" value="Update My Cart" src="',
            2 => 41,
        ],
        182 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        183 => [
            0 => 'String',
            1 => '\'update.gif\'',
            2 => 43,
        ],
        184 => [
            0 => 'Pipe',
            1 => '|',
            2 => 43,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 43,
        ],
        186 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        187 => [
            0 => 'TextData',
            1 => '" />
        Subtotal:
        <span class="money">',
            2 => 43,
        ],
        188 => [
            0 => 'VariableStart',
            1 => '',
            2 => 45,
        ],
        189 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 45,
        ],
        190 => [
            0 => 'Dot',
            1 => '.',
            2 => 45,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 45,
        ],
        192 => [
            0 => 'Pipe',
            1 => '|',
            2 => 45,
        ],
        193 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 45,
        ],
        194 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 45,
        ],
        195 => [
            0 => 'TextData',
            1 => '</span>
      </li>
    </ul>

  </div>

    <div class="info">
    <input type="image" value="Checkout!" name="checkout" src="',
            2 => 45,
        ],
        196 => [
            0 => 'VariableStart',
            1 => '',
            2 => 52,
        ],
        197 => [
            0 => 'String',
            1 => '\'checkout.gif\'',
            2 => 52,
        ],
        198 => [
            0 => 'Pipe',
            1 => '|',
            2 => 52,
        ],
        199 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 52,
        ],
        200 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 52,
        ],
        201 => [
            0 => 'TextData',
            1 => '" />
    </div>

    ',
            2 => 52,
        ],
        202 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        203 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 55,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'additional_checkout_buttons',
            2 => 55,
        ],
        205 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        206 => [
            0 => 'TextData',
            1 => '
    <div class="additional-checkout-buttons">
      <p>- or -</p>
      ',
            2 => 55,
        ],
        207 => [
            0 => 'VariableStart',
            1 => '',
            2 => 58,
        ],
        208 => [
            0 => 'Identifier',
            1 => 'content_for_additional_checkout_buttons',
            2 => 58,
        ],
        209 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 58,
        ],
        210 => [
            0 => 'TextData',
            1 => '
    </div>
    ',
            2 => 58,
        ],
        211 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        212 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 60,
        ],
        213 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        214 => [
            0 => 'TextData',
            1 => '

  </form>

  ',
            2 => 60,
        ],
        215 => [
            0 => 'BlockStart',
            1 => '',
            2 => 64,
        ],
        216 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 64,
        ],
        217 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 64,
        ],
        218 => [
            0 => 'TextData',
            1 => '

</div>
',
            2 => 64,
        ],
    ],
    'performance/tests/dropify/collection.liquid' => [
        0 => [
            0 => 'BlockStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 1,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 1,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 1,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 1,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 1,
        ],
        6 => [
            0 => 'Number',
            1 => '20',
            2 => 1,
        ],
        7 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 1,
        ],
        8 => [
            0 => 'TextData',
            1 => '

<ul id="product-collection">
    ',
            2 => 1,
        ],
        9 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 4,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 4,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 4,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 4,
        ],
        14 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 4,
        ],
        16 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        17 => [
            0 => 'TextData',
            1 => '
    <li class="singleproduct clearfix">
      <div class="small">
        <div class="prodimage"><a href="',
            2 => 4,
        ],
        18 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 7,
        ],
        22 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        23 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 7,
        ],
        24 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 7,
        ],
        28 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 7,
        ],
        30 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        31 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 7,
        ],
        32 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        33 => [
            0 => 'TextData',
            1 => '" /></a></div>
      </div>
      <div class="description">
        <h3><a href="',
            2 => 7,
        ],
        34 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        36 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 10,
        ],
        38 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        39 => [
            0 => 'TextData',
            1 => '">',
            2 => 10,
        ],
        40 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        42 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 10,
        ],
        44 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        45 => [
            0 => 'TextData',
            1 => '</a></h3>
        <p>',
            2 => 10,
        ],
        46 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        48 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 11,
        ],
        50 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 11,
        ],
        52 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 11,
        ],
        54 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        55 => [
            0 => 'Number',
            1 => '35',
            2 => 11,
        ],
        56 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        57 => [
            0 => 'TextData',
            1 => '</p>
      <p class="money">',
            2 => 11,
        ],
        58 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        60 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 12,
        ],
        62 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 12,
        ],
        64 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        65 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 12,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'price_varies',
            2 => 12,
        ],
        70 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        71 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 12,
        ],
        72 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        74 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'price_max',
            2 => 12,
        ],
        76 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 12,
        ],
        78 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        79 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 12,
        ],
        81 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        82 => [
            0 => 'TextData',
            1 => '</p>
     </div>
    </li>
    ',
            2 => 12,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 15,
        ],
        85 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        86 => [
            0 => 'TextData',
            1 => '
</ul>

<div id="pagination">
  ',
            2 => 15,
        ],
        87 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 19,
        ],
        89 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        90 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 19,
        ],
        91 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        92 => [
            0 => 'TextData',
            1 => '
</div>

',
            2 => 19,
        ],
        93 => [
            0 => 'BlockStart',
            1 => '',
            2 => 22,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 22,
        ],
        95 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 22,
        ],
        96 => [
            0 => 'TextData',
            1 => '
',
            2 => 22,
        ],
    ],
    'performance/tests/dropify/index.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="frontproducts"><div id="frontproducts-top"><div id="frontproducts-bottom">
<h2 style="display: none;">Featured Items</h2>
',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 3,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 3,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 3,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 3,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 3,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 3,
        ],
        8 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 3,
        ],
        11 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        12 => [
            0 => 'Number',
            1 => '1',
            2 => 3,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'offset',
            2 => 3,
        ],
        14 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        15 => [
            0 => 'Number',
            1 => '0',
            2 => 3,
        ],
        16 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 3,
        ],
        17 => [
            0 => 'TextData',
            1 => '
  <div class="productmain">
   <a href="',
            2 => 3,
        ],
        18 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 5,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 5,
        ],
        22 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        23 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 5,
        ],
        24 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 5,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 5,
        ],
        28 => [
            0 => 'Pipe',
            1 => '|',
            2 => 5,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 5,
        ],
        30 => [
            0 => 'Colon',
            1 => ':',
            2 => 5,
        ],
        31 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 5,
        ],
        32 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        33 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 5,
        ],
        34 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 5,
        ],
        36 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 5,
        ],
        38 => [
            0 => 'Pipe',
            1 => '|',
            2 => 5,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 5,
        ],
        40 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        41 => [
            0 => 'TextData',
            1 => '" /></a>
   <h3><a href="',
            2 => 5,
        ],
        42 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        44 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 6,
        ],
        46 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        47 => [
            0 => 'TextData',
            1 => '">',
            2 => 6,
        ],
        48 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        50 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 6,
        ],
        52 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        53 => [
            0 => 'TextData',
            1 => '</a></h3>
   <div class="description">',
            2 => 6,
        ],
        54 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        56 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 7,
        ],
        58 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 7,
        ],
        60 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 7,
        ],
        62 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        63 => [
            0 => 'Number',
            1 => '18',
            2 => 7,
        ],
        64 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        65 => [
            0 => 'TextData',
            1 => '</div>
  <p class="money">',
            2 => 7,
        ],
        66 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 8,
        ],
        70 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 8,
        ],
        72 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        73 => [
            0 => 'TextData',
            1 => '</p>
  </div>
',
            2 => 8,
        ],
        74 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 10,
        ],
        76 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        77 => [
            0 => 'TextData',
            1 => '
',
            2 => 10,
        ],
        78 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 11,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 11,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 11,
        ],
        83 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 11,
        ],
        85 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 11,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'offset',
            2 => 11,
        ],
        88 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        89 => [
            0 => 'Number',
            1 => '1',
            2 => 11,
        ],
        90 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        91 => [
            0 => 'TextData',
            1 => '
  <div class="product">
   <a href="',
            2 => 11,
        ],
        92 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        94 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 13,
        ],
        96 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        97 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 13,
        ],
        98 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        100 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 13,
        ],
        102 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 13,
        ],
        104 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        105 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 13,
        ],
        106 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        107 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 13,
        ],
        108 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        110 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 13,
        ],
        112 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 13,
        ],
        114 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        115 => [
            0 => 'TextData',
            1 => '" /></a>
   <h3><a href="',
            2 => 13,
        ],
        116 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 14,
        ],
        118 => [
            0 => 'Dot',
            1 => '.',
            2 => 14,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 14,
        ],
        120 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        121 => [
            0 => 'TextData',
            1 => '">',
            2 => 14,
        ],
        122 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 14,
        ],
        124 => [
            0 => 'Dot',
            1 => '.',
            2 => 14,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 14,
        ],
        126 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        127 => [
            0 => 'TextData',
            1 => '</a></h3>
     <p class="money">',
            2 => 14,
        ],
        128 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        130 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 15,
        ],
        132 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 15,
        ],
        134 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        135 => [
            0 => 'TextData',
            1 => '</p>
  </div>
',
            2 => 15,
        ],
        136 => [
            0 => 'BlockStart',
            1 => '',
            2 => 17,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 17,
        ],
        138 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 17,
        ],
        139 => [
            0 => 'TextData',
            1 => '
</div></div></div>

<div id="mainarticle">
  ',
            2 => 17,
        ],
        140 => [
            0 => 'BlockStart',
            1 => '',
            2 => 21,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'assign',
            2 => 21,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 21,
        ],
        143 => [
            0 => 'Equals',
            1 => '=',
            2 => 21,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 21,
        ],
        145 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 21,
        ],
        147 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 21,
        ],
        148 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 21,
        ],
        149 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 23,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 23,
        ],
        152 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 23,
        ],
        154 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 23,
        ],
        155 => [
            0 => 'String',
            1 => '""',
            2 => 23,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        157 => [
            0 => 'TextData',
            1 => '
    <h2>',
            2 => 23,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 24,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 24,
        ],
        162 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        163 => [
            0 => 'TextData',
            1 => '</h2>
    <div class="article-body textile">
      ',
            2 => 24,
        ],
        164 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 26,
        ],
        166 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 26,
        ],
        168 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        169 => [
            0 => 'TextData',
            1 => '
    </div>
  ',
            2 => 26,
        ],
        170 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 28,
        ],
        172 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        173 => [
            0 => 'TextData',
            1 => '
    <div class="article-body textile">
    In <em>Admin &gt; Blogs &amp; Pages</em>, create a page with the handle <strong><code>frontpage</code></strong> and it will show up here.<br />
    ',
            2 => 28,
        ],
        174 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        175 => [
            0 => 'String',
            1 => '"Learn more about handles"',
            2 => 31,
        ],
        176 => [
            0 => 'Pipe',
            1 => '|',
            2 => 31,
        ],
        177 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 31,
        ],
        178 => [
            0 => 'Colon',
            1 => ':',
            2 => 31,
        ],
        179 => [
            0 => 'String',
            1 => '"http://wiki.shopify.com/Handle"',
            2 => 31,
        ],
        180 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        181 => [
            0 => 'TextData',
            1 => '
    </div>
  ',
            2 => 31,
        ],
        182 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        183 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 33,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        185 => [
            0 => 'TextData',
            1 => '

</div>
<br style="clear: both;" />
<div id="articles">
  ',
            2 => 33,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 38,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 38,
        ],
        189 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 38,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'blogs',
            2 => 38,
        ],
        191 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'news',
            2 => 38,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 38,
        ],
        195 => [
            0 => 'Identifier',
            1 => 'offset',
            2 => 38,
        ],
        196 => [
            0 => 'Colon',
            1 => ':',
            2 => 38,
        ],
        197 => [
            0 => 'Number',
            1 => '1',
            2 => 38,
        ],
        198 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        199 => [
            0 => 'TextData',
            1 => '
    <div class="article">
    <h2>',
            2 => 38,
        ],
        200 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        201 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 40,
        ],
        202 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        203 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 40,
        ],
        204 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        205 => [
            0 => 'TextData',
            1 => '</h2>
    <div class="article-body textile">
      ',
            2 => 40,
        ],
        206 => [
            0 => 'VariableStart',
            1 => '',
            2 => 42,
        ],
        207 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 42,
        ],
        208 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        209 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 42,
        ],
        210 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 42,
        ],
        211 => [
            0 => 'TextData',
            1 => '
    </div>
  </div>
  ',
            2 => 42,
        ],
        212 => [
            0 => 'BlockStart',
            1 => '',
            2 => 45,
        ],
        213 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 45,
        ],
        214 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 45,
        ],
        215 => [
            0 => 'TextData',
            1 => '
</div>

',
            2 => 45,
        ],
    ],
    'performance/tests/dropify/page.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="page">
  <h2>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>

  <div class="article textile">
    ',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 5,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 5,
        ],
        11 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        12 => [
            0 => 'TextData',
            1 => '
  </div>

</div>
',
            2 => 5,
        ],
    ],
    'performance/tests/dropify/product.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="productpage">

  <div id="productimages"><div id="productimages-top"><div id="productimages-bottom">
    ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 4,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 4,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 4,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 4,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 4,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        9 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 4,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 5,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 5,
        ],
        13 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 5,
        ],
        15 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        16 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 5,
        ],
        17 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 6,
        ],
        19 => [
            0 => 'Pipe',
            1 => '|',
            2 => 6,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 6,
        ],
        21 => [
            0 => 'Colon',
            1 => ':',
            2 => 6,
        ],
        22 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 6,
        ],
        23 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        24 => [
            0 => 'TextData',
            1 => '" class="productimage" rel="lightbox">
          <img src="',
            2 => 6,
        ],
        25 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 7,
        ],
        27 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 7,
        ],
        29 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        30 => [
            0 => 'String',
            1 => '\'medium\'',
            2 => 7,
        ],
        31 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        32 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 7,
        ],
        33 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        35 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 7,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 7,
        ],
        39 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        40 => [
            0 => 'TextData',
            1 => '" />
        </a>
      ',
            2 => 7,
        ],
        41 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 9,
        ],
        43 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        44 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 9,
        ],
        45 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 10,
        ],
        47 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 10,
        ],
        49 => [
            0 => 'Colon',
            1 => ':',
            2 => 10,
        ],
        50 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 10,
        ],
        51 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        52 => [
            0 => 'TextData',
            1 => '" class="productimage-small" rel="lightbox">
          <img src="',
            2 => 10,
        ],
        53 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 11,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 11,
        ],
        57 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        58 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 11,
        ],
        59 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        60 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 11,
        ],
        61 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        63 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 11,
        ],
        65 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 11,
        ],
        67 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        68 => [
            0 => 'TextData',
            1 => '" />
        </a>
      ',
            2 => 11,
        ],
        69 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 13,
        ],
        71 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        72 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 13,
        ],
        73 => [
            0 => 'BlockStart',
            1 => '',
            2 => 14,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 14,
        ],
        75 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 14,
        ],
        76 => [
            0 => 'TextData',
            1 => '
  </div></div></div>

  <h2>',
            2 => 14,
        ],
        77 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 17,
        ],
        79 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 17,
        ],
        81 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        82 => [
            0 => 'TextData',
            1 => '</h2>

  <ul id="details" class="hlist">
    <li>Vendor: ',
            2 => 17,
        ],
        83 => [
            0 => 'VariableStart',
            1 => '',
            2 => 20,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 20,
        ],
        85 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'vendor',
            2 => 20,
        ],
        87 => [
            0 => 'Pipe',
            1 => '|',
            2 => 20,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'link_to_vendor',
            2 => 20,
        ],
        89 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 20,
        ],
        90 => [
            0 => 'TextData',
            1 => '</li>
    <li>Type: ',
            2 => 20,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 21,
        ],
        93 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'type',
            2 => 21,
        ],
        95 => [
            0 => 'Pipe',
            1 => '|',
            2 => 21,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'link_to_type',
            2 => 21,
        ],
        97 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        98 => [
            0 => 'TextData',
            1 => '</li>
  </ul>

  <small>',
            2 => 21,
        ],
        99 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        101 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 24,
        ],
        103 => [
            0 => 'Pipe',
            1 => '|',
            2 => 24,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 24,
        ],
        105 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        106 => [
            0 => 'BlockStart',
            1 => '',
            2 => 24,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 24,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        109 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'price_varies',
            2 => 24,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 24,
        ],
        112 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 24,
        ],
        113 => [
            0 => 'VariableStart',
            1 => '',
            2 => 24,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        115 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'price_max',
            2 => 24,
        ],
        117 => [
            0 => 'Pipe',
            1 => '|',
            2 => 24,
        ],
        118 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 24,
        ],
        119 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 24,
        ],
        120 => [
            0 => 'BlockStart',
            1 => '',
            2 => 24,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 24,
        ],
        122 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 24,
        ],
        123 => [
            0 => 'TextData',
            1 => '</small>

  <div id="variant-add">
    <form action="/cart/add" method="post">

      <select id="variant-select" name="id" class="product-info-options">
        ',
            2 => 24,
        ],
        124 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 30,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 30,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 30,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 30,
        ],
        129 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 30,
        ],
        131 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        132 => [
            0 => 'TextData',
            1 => '
          <option value="',
            2 => 30,
        ],
        133 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 31,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 31,
        ],
        137 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        138 => [
            0 => 'TextData',
            1 => '">',
            2 => 31,
        ],
        139 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 31,
        ],
        141 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 31,
        ],
        143 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        144 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 31,
        ],
        145 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 31,
        ],
        147 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 31,
        ],
        149 => [
            0 => 'Pipe',
            1 => '|',
            2 => 31,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 31,
        ],
        151 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        152 => [
            0 => 'TextData',
            1 => '</option>
        ',
            2 => 31,
        ],
        153 => [
            0 => 'BlockStart',
            1 => '',
            2 => 32,
        ],
        154 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 32,
        ],
        155 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 32,
        ],
        156 => [
            0 => 'TextData',
            1 => '
      </select>

      <div id="price-field" class="price"></div>

    <div style="text-align:center;"><input type="image" name="add" value="Add to Cart" id="add" src="',
            2 => 32,
        ],
        157 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        158 => [
            0 => 'String',
            1 => '\'addtocart.gif\'',
            2 => 37,
        ],
        159 => [
            0 => 'Pipe',
            1 => '|',
            2 => 37,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 37,
        ],
        161 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        162 => [
            0 => 'TextData',
            1 => '" /></div>
    </form>
  </div>

  <div class="description textile">
    ',
            2 => 37,
        ],
        163 => [
            0 => 'VariableStart',
            1 => '',
            2 => 42,
        ],
        164 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 42,
        ],
        165 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 42,
        ],
        167 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 42,
        ],
        168 => [
            0 => 'TextData',
            1 => '
  </div>
</div>

<script type="text/javascript">
<!--
  // prototype callback for multi variants dropdown selector
  var selectCallback = function(variant, selector) {
    if (variant && variant.available == true) {
      // selected a valid variant
      $(\'add\').removeClassName(\'disabled\'); // remove unavailable class from add-to-cart button
      $(\'add\').disabled = false;           // reenable add-to-cart button
      $(\'price-field\').innerHTML = Shopify.formatMoney(variant.price, "',
            2 => 42,
        ],
        169 => [
            0 => 'VariableStart',
            1 => '',
            2 => 54,
        ],
        170 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 54,
        ],
        171 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        172 => [
            0 => 'Identifier',
            1 => 'money_with_currency_format',
            2 => 54,
        ],
        173 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 54,
        ],
        174 => [
            0 => 'TextData',
            1 => '");  // update price field
    } else {
      // variant doesn\'t exist
      $(\'add\').addClassName(\'disabled\');      // set add-to-cart button to unavailable class
      $(\'add\').disabled = true;              // disable add-to-cart button
      $(\'price-field\').innerHTML = (variant) ? "Sold Out" : "Unavailable"; // update price-field message
    }
  };

  // initialize multi selector for product
  Event.observe(document, \'dom:loaded\', function() {
    new Shopify.OptionSelectors("variant-select", { product: ',
            2 => 54,
        ],
        175 => [
            0 => 'VariableStart',
            1 => '',
            2 => 65,
        ],
        176 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 65,
        ],
        177 => [
            0 => 'Pipe',
            1 => '|',
            2 => 65,
        ],
        178 => [
            0 => 'Identifier',
            1 => 'json',
            2 => 65,
        ],
        179 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 65,
        ],
        180 => [
            0 => 'TextData',
            1 => ', onVariantSelected: selectCallback });
  });
-->
</script>
',
            2 => 65,
        ],
    ],
    'performance/tests/dropify/theme.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <title>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 7,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 7,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        6 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 7,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page_title',
            2 => 7,
        ],
        9 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        10 => [
            0 => 'TextData',
            1 => '</title>

  ',
            2 => 7,
        ],
        11 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        12 => [
            0 => 'String',
            1 => '\'textile.css\'',
            2 => 9,
        ],
        13 => [
            0 => 'Pipe',
            1 => '|',
            2 => 9,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 9,
        ],
        15 => [
            0 => 'Pipe',
            1 => '|',
            2 => 9,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 9,
        ],
        17 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        18 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 9,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        20 => [
            0 => 'String',
            1 => '\'lightbox/v204/lightbox.css\'',
            2 => 10,
        ],
        21 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 10,
        ],
        23 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 10,
        ],
        25 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        26 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 10,
        ],
        27 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        28 => [
            0 => 'String',
            1 => '\'prototype/1.6/prototype.js\'',
            2 => 12,
        ],
        29 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 12,
        ],
        31 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 12,
        ],
        33 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        34 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 12,
        ],
        35 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        36 => [
            0 => 'String',
            1 => '\'scriptaculous/1.8.2/scriptaculous.js\'',
            2 => 13,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 13,
        ],
        39 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 13,
        ],
        41 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        42 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 13,
        ],
        43 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        44 => [
            0 => 'String',
            1 => '\'lightbox/v204/lightbox.js\'',
            2 => 14,
        ],
        45 => [
            0 => 'Pipe',
            1 => '|',
            2 => 14,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 14,
        ],
        47 => [
            0 => 'Pipe',
            1 => '|',
            2 => 14,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 14,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        50 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 14,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        52 => [
            0 => 'String',
            1 => '\'option_selection.js\'',
            2 => 15,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'shopify_asset_url',
            2 => 15,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 15,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        58 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 15,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        60 => [
            0 => 'String',
            1 => '\'layout.css\'',
            2 => 17,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 17,
        ],
        63 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 17,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        66 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 17,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        68 => [
            0 => 'String',
            1 => '\'shop.js\'',
            2 => 18,
        ],
        69 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 18,
        ],
        71 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 18,
        ],
        73 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        74 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 18,
        ],
        75 => [
            0 => 'VariableStart',
            1 => '',
            2 => 20,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'content_for_header',
            2 => 20,
        ],
        77 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 20,
        ],
        78 => [
            0 => 'TextData',
            1 => '
</head>

<body id="page-',
            2 => 20,
        ],
        79 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 23,
        ],
        81 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        82 => [
            0 => 'TextData',
            1 => '">

  <p class="hide"><a href="#rightsiders">Skip to navigation.</a></p>
    <!-- mini cart -->
        ',
            2 => 23,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 27,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 27,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 27,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 27,
        ],
        88 => [
            0 => 'Comparison',
            1 => '>',
            2 => 27,
        ],
        89 => [
            0 => 'Number',
            1 => '0',
            2 => 27,
        ],
        90 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 27,
        ],
        91 => [
            0 => 'TextData',
            1 => '
      <div id="minicart" style="display:none;"><div id="minicart-inner">
      <div id="minicart-items">
      <h2>There ',
            2 => 27,
        ],
        92 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 30,
        ],
        94 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 30,
        ],
        96 => [
            0 => 'Pipe',
            1 => '|',
            2 => 30,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 30,
        ],
        98 => [
            0 => 'Colon',
            1 => ':',
            2 => 30,
        ],
        99 => [
            0 => 'String',
            1 => '\'is\'',
            2 => 30,
        ],
        100 => [
            0 => 'Comma',
            1 => ',',
            2 => 30,
        ],
        101 => [
            0 => 'String',
            1 => '\'are\'',
            2 => 30,
        ],
        102 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        103 => [
            0 => 'TextData',
            1 => ' ',
            2 => 30,
        ],
        104 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 30,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 30,
        ],
        108 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        109 => [
            0 => 'TextData',
            1 => ' ',
            2 => 30,
        ],
        110 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 30,
        ],
        112 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 30,
        ],
        114 => [
            0 => 'Pipe',
            1 => '|',
            2 => 30,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 30,
        ],
        116 => [
            0 => 'Colon',
            1 => ':',
            2 => 30,
        ],
        117 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 30,
        ],
        118 => [
            0 => 'Comma',
            1 => ',',
            2 => 30,
        ],
        119 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 30,
        ],
        120 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        121 => [
            0 => 'TextData',
            1 => ' in <a href="/cart" title="View your cart">your cart</a>!</h2><h4 style="font-size: 16px; margin: 0 0 10px 0; padding: 0;">Your subtotal is ',
            2 => 30,
        ],
        122 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 30,
        ],
        124 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 30,
        ],
        126 => [
            0 => 'Pipe',
            1 => '|',
            2 => 30,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 30,
        ],
        128 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        129 => [
            0 => 'TextData',
            1 => '.</h4>
        ',
            2 => 30,
        ],
        130 => [
            0 => 'BlockStart',
            1 => '',
            2 => 31,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 31,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 31,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 31,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 31,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'items',
            2 => 31,
        ],
        137 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 31,
        ],
        138 => [
            0 => 'TextData',
            1 => '
        <div class="thumb">
          <div class="prodimage"><a href="',
            2 => 31,
        ],
        139 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        141 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 33,
        ],
        143 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 33,
        ],
        145 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        146 => [
            0 => 'TextData',
            1 => '" onMouseover="tooltip(\'',
            2 => 33,
        ],
        147 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        149 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'quantity',
            2 => 33,
        ],
        151 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        152 => [
            0 => 'TextData',
            1 => ' x ',
            2 => 33,
        ],
        153 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        154 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        155 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        156 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 33,
        ],
        157 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        158 => [
            0 => 'TextData',
            1 => ' (',
            2 => 33,
        ],
        159 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        161 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 33,
        ],
        163 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        164 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 33,
        ],
        165 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        166 => [
            0 => 'TextData',
            1 => ')\', 200)"; onMouseout="hidetooltip()"><img src="',
            2 => 33,
        ],
        167 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        169 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        170 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 33,
        ],
        171 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        172 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 33,
        ],
        173 => [
            0 => 'Pipe',
            1 => '|',
            2 => 33,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 33,
        ],
        175 => [
            0 => 'Colon',
            1 => ':',
            2 => 33,
        ],
        176 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 33,
        ],
        177 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        178 => [
            0 => 'TextData',
            1 => '" /></a></div>
        </div>
        ',
            2 => 33,
        ],
        179 => [
            0 => 'BlockStart',
            1 => '',
            2 => 35,
        ],
        180 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 35,
        ],
        181 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 35,
        ],
        182 => [
            0 => 'TextData',
            1 => '
        </div>
       <br style="clear:both;" />
      </div></div>
        ',
            2 => 35,
        ],
        183 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        184 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 39,
        ],
        185 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        186 => [
            0 => 'TextData',
            1 => '

  <div id="container">
    <div id="header">
      <!-- Begin Header -->
        <h1 id="logo"><a href="/" title="Go Home">',
            2 => 39,
        ],
        187 => [
            0 => 'VariableStart',
            1 => '',
            2 => 44,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 44,
        ],
        189 => [
            0 => 'Dot',
            1 => '.',
            2 => 44,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 44,
        ],
        191 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 44,
        ],
        192 => [
            0 => 'TextData',
            1 => '</a></h1>
      <div id="cartlinks">
        ',
            2 => 44,
        ],
        193 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 46,
        ],
        195 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 46,
        ],
        196 => [
            0 => 'Dot',
            1 => '.',
            2 => 46,
        ],
        197 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 46,
        ],
        198 => [
            0 => 'Comparison',
            1 => '>',
            2 => 46,
        ],
        199 => [
            0 => 'Number',
            1 => '0',
            2 => 46,
        ],
        200 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        201 => [
            0 => 'TextData',
            1 => '
          <h2 id="cartcount"><a href="/cart" onMouseover="tooltip(\'There ',
            2 => 46,
        ],
        202 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        203 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 47,
        ],
        204 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        205 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 47,
        ],
        206 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        207 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 47,
        ],
        208 => [
            0 => 'Colon',
            1 => ':',
            2 => 47,
        ],
        209 => [
            0 => 'String',
            1 => '\'is\'',
            2 => 47,
        ],
        210 => [
            0 => 'Comma',
            1 => ',',
            2 => 47,
        ],
        211 => [
            0 => 'String',
            1 => '\'are\'',
            2 => 47,
        ],
        212 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        213 => [
            0 => 'TextData',
            1 => ' ',
            2 => 47,
        ],
        214 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        215 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 47,
        ],
        216 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        217 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 47,
        ],
        218 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        219 => [
            0 => 'TextData',
            1 => ' ',
            2 => 47,
        ],
        220 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        221 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 47,
        ],
        222 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        223 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 47,
        ],
        224 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        225 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 47,
        ],
        226 => [
            0 => 'Colon',
            1 => ':',
            2 => 47,
        ],
        227 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 47,
        ],
        228 => [
            0 => 'Comma',
            1 => ',',
            2 => 47,
        ],
        229 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 47,
        ],
        230 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        231 => [
            0 => 'TextData',
            1 => ' in your cart!\', 200)"; onMouseout="hidetooltip()">',
            2 => 47,
        ],
        232 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        233 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 47,
        ],
        234 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        235 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 47,
        ],
        236 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        237 => [
            0 => 'TextData',
            1 => ' ',
            2 => 47,
        ],
        238 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        239 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 47,
        ],
        240 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        241 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 47,
        ],
        242 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        243 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 47,
        ],
        244 => [
            0 => 'Colon',
            1 => ':',
            2 => 47,
        ],
        245 => [
            0 => 'String',
            1 => '\'thing\'',
            2 => 47,
        ],
        246 => [
            0 => 'Comma',
            1 => ',',
            2 => 47,
        ],
        247 => [
            0 => 'String',
            1 => '\'things\'',
            2 => 47,
        ],
        248 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        249 => [
            0 => 'TextData',
            1 => '!</a></h2>
      <a href="/cart" id="minicartswitch" onclick="superSwitch(this, \'minicart\', \'Close Mini Cart\'); return false;" id="cartswitch">View Mini Cart (',
            2 => 47,
        ],
        250 => [
            0 => 'VariableStart',
            1 => '',
            2 => 48,
        ],
        251 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 48,
        ],
        252 => [
            0 => 'Dot',
            1 => '.',
            2 => 48,
        ],
        253 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 48,
        ],
        254 => [
            0 => 'Pipe',
            1 => '|',
            2 => 48,
        ],
        255 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 48,
        ],
        256 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 48,
        ],
        257 => [
            0 => 'TextData',
            1 => ')</a>
        ',
            2 => 48,
        ],
        258 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        259 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 49,
        ],
        260 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        261 => [
            0 => 'TextData',
            1 => '
      </div>
      <!-- End Header -->

    </div>
  <hr />
<div id="main">

    <div id="content">
    <div id="innercontent">
      ',
            2 => 49,
        ],
        262 => [
            0 => 'VariableStart',
            1 => '',
            2 => 59,
        ],
        263 => [
            0 => 'Identifier',
            1 => 'content_for_layout',
            2 => 59,
        ],
        264 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 59,
        ],
        265 => [
            0 => 'TextData',
            1 => '
      </div>
    </div>

  <hr />
    <div id="rightsiders">

      <ul class="rightlinks">
        ',
            2 => 59,
        ],
        266 => [
            0 => 'BlockStart',
            1 => '',
            2 => 67,
        ],
        267 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 67,
        ],
        268 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 67,
        ],
        269 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 67,
        ],
        270 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 67,
        ],
        271 => [
            0 => 'Dot',
            1 => '.',
            2 => 67,
        ],
        272 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 67,
        ],
        273 => [
            0 => 'Dot',
            1 => '.',
            2 => 67,
        ],
        274 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 67,
        ],
        275 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 67,
        ],
        276 => [
            0 => 'TextData',
            1 => '
           <li>',
            2 => 67,
        ],
        277 => [
            0 => 'VariableStart',
            1 => '',
            2 => 68,
        ],
        278 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 68,
        ],
        279 => [
            0 => 'Dot',
            1 => '.',
            2 => 68,
        ],
        280 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 68,
        ],
        281 => [
            0 => 'Pipe',
            1 => '|',
            2 => 68,
        ],
        282 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 68,
        ],
        283 => [
            0 => 'Colon',
            1 => ':',
            2 => 68,
        ],
        284 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 68,
        ],
        285 => [
            0 => 'Dot',
            1 => '.',
            2 => 68,
        ],
        286 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 68,
        ],
        287 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 68,
        ],
        288 => [
            0 => 'TextData',
            1 => '</li>
        ',
            2 => 68,
        ],
        289 => [
            0 => 'BlockStart',
            1 => '',
            2 => 69,
        ],
        290 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 69,
        ],
        291 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 69,
        ],
        292 => [
            0 => 'TextData',
            1 => '
      </ul>

        ',
            2 => 69,
        ],
        293 => [
            0 => 'BlockStart',
            1 => '',
            2 => 72,
        ],
        294 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 72,
        ],
        295 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 72,
        ],
        296 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 72,
        ],
        297 => [
            0 => 'TextData',
            1 => '
        <ul class="rightlinks">
          ',
            2 => 72,
        ],
        298 => [
            0 => 'BlockStart',
            1 => '',
            2 => 74,
        ],
        299 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 74,
        ],
        300 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 74,
        ],
        301 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 74,
        ],
        302 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 74,
        ],
        303 => [
            0 => 'Dot',
            1 => '.',
            2 => 74,
        ],
        304 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 74,
        ],
        305 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 74,
        ],
        306 => [
            0 => 'TextData',
            1 => '
            <li><span class="add-link">',
            2 => 74,
        ],
        307 => [
            0 => 'VariableStart',
            1 => '',
            2 => 75,
        ],
        308 => [
            0 => 'String',
            1 => '\'+\'',
            2 => 75,
        ],
        309 => [
            0 => 'Pipe',
            1 => '|',
            2 => 75,
        ],
        310 => [
            0 => 'Identifier',
            1 => 'link_to_add_tag',
            2 => 75,
        ],
        311 => [
            0 => 'Colon',
            1 => ':',
            2 => 75,
        ],
        312 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 75,
        ],
        313 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 75,
        ],
        314 => [
            0 => 'TextData',
            1 => '</span>',
            2 => 75,
        ],
        315 => [
            0 => 'VariableStart',
            1 => '',
            2 => 75,
        ],
        316 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 75,
        ],
        317 => [
            0 => 'Pipe',
            1 => '|',
            2 => 75,
        ],
        318 => [
            0 => 'Identifier',
            1 => 'highlight_active_tag',
            2 => 75,
        ],
        319 => [
            0 => 'Pipe',
            1 => '|',
            2 => 75,
        ],
        320 => [
            0 => 'Identifier',
            1 => 'link_to_tag',
            2 => 75,
        ],
        321 => [
            0 => 'Colon',
            1 => ':',
            2 => 75,
        ],
        322 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 75,
        ],
        323 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 75,
        ],
        324 => [
            0 => 'TextData',
            1 => '</li>
          ',
            2 => 75,
        ],
        325 => [
            0 => 'BlockStart',
            1 => '',
            2 => 76,
        ],
        326 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 76,
        ],
        327 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 76,
        ],
        328 => [
            0 => 'TextData',
            1 => '
        </ul>
        ',
            2 => 76,
        ],
        329 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        330 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 78,
        ],
        331 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        332 => [
            0 => 'TextData',
            1 => '

      <ul class="rightlinks">
        ',
            2 => 78,
        ],
        333 => [
            0 => 'BlockStart',
            1 => '',
            2 => 81,
        ],
        334 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 81,
        ],
        335 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 81,
        ],
        336 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 81,
        ],
        337 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 81,
        ],
        338 => [
            0 => 'Dot',
            1 => '.',
            2 => 81,
        ],
        339 => [
            0 => 'Identifier',
            1 => 'footer',
            2 => 81,
        ],
        340 => [
            0 => 'Dot',
            1 => '.',
            2 => 81,
        ],
        341 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 81,
        ],
        342 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 81,
        ],
        343 => [
            0 => 'TextData',
            1 => '
           <li>',
            2 => 81,
        ],
        344 => [
            0 => 'VariableStart',
            1 => '',
            2 => 82,
        ],
        345 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 82,
        ],
        346 => [
            0 => 'Dot',
            1 => '.',
            2 => 82,
        ],
        347 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 82,
        ],
        348 => [
            0 => 'Pipe',
            1 => '|',
            2 => 82,
        ],
        349 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 82,
        ],
        350 => [
            0 => 'Colon',
            1 => ':',
            2 => 82,
        ],
        351 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 82,
        ],
        352 => [
            0 => 'Dot',
            1 => '.',
            2 => 82,
        ],
        353 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 82,
        ],
        354 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 82,
        ],
        355 => [
            0 => 'TextData',
            1 => '</li>
        ',
            2 => 82,
        ],
        356 => [
            0 => 'BlockStart',
            1 => '',
            2 => 83,
        ],
        357 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 83,
        ],
        358 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 83,
        ],
        359 => [
            0 => 'TextData',
            1 => '
      </ul>

    </div>

  <hr /><br style="clear:both;" />

    <div id="footer">
      <div class="footerinner">
      All prices are in ',
            2 => 83,
        ],
        360 => [
            0 => 'VariableStart',
            1 => '',
            2 => 92,
        ],
        361 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 92,
        ],
        362 => [
            0 => 'Dot',
            1 => '.',
            2 => 92,
        ],
        363 => [
            0 => 'Identifier',
            1 => 'currency',
            2 => 92,
        ],
        364 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 92,
        ],
        365 => [
            0 => 'TextData',
            1 => '.
      Powered by <a href="http://www.shopify.com" title="Shopify, Hosted E-Commerce">Shopify</a>.
    </div>
    </div>

  </div>
</div>

<div id="tooltip"></div>
<img id="pointer" src="',
            2 => 92,
        ],
        366 => [
            0 => 'VariableStart',
            1 => '',
            2 => 101,
        ],
        367 => [
            0 => 'String',
            1 => '\'arrow2.gif\'',
            2 => 101,
        ],
        368 => [
            0 => 'Pipe',
            1 => '|',
            2 => 101,
        ],
        369 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 101,
        ],
        370 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 101,
        ],
        371 => [
            0 => 'TextData',
            1 => '" />

</body>
</html>

',
            2 => 101,
        ],
    ],
    'performance/tests/ripen/article.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div class="article">
  <h2 class="article-title">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>
  <p class="article-details">posted <span class="article-time">',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 3,
        ],
        11 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 3,
        ],
        13 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        14 => [
            0 => 'String',
            1 => '"%Y %h"',
            2 => 3,
        ],
        15 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        16 => [
            0 => 'TextData',
            1 => '</span> by <span class="article-author">',
            2 => 3,
        ],
        17 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        19 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 3,
        ],
        21 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        22 => [
            0 => 'TextData',
            1 => '</span></p>

  <div class="article-body textile">
    ',
            2 => 3,
        ],
        23 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        25 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 6,
        ],
        27 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        28 => [
            0 => 'TextData',
            1 => '
  </div>

</div>

<!-- Comments -->
',
            2 => 6,
        ],
        29 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 12,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 12,
        ],
        32 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 12,
        ],
        34 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        35 => [
            0 => 'TextData',
            1 => '
<div id="comments">
  <h3>Comments</h3>

  <!-- List all comments -->
  <ul id="comment-list">
  ',
            2 => 12,
        ],
        36 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 18,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 18,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 18,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 18,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'comments',
            2 => 18,
        ],
        43 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        44 => [
            0 => 'TextData',
            1 => '
    <li>
      <div class="comment">
        ',
            2 => 18,
        ],
        45 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 21,
        ],
        47 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 21,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        50 => [
            0 => 'TextData',
            1 => '
      </div>

      <div class="comment-details">
        Posted by ',
            2 => 21,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 25,
        ],
        53 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 25,
        ],
        55 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        56 => [
            0 => 'TextData',
            1 => ' on ',
            2 => 25,
        ],
        57 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 25,
        ],
        59 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 25,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 25,
        ],
        63 => [
            0 => 'Colon',
            1 => ':',
            2 => 25,
        ],
        64 => [
            0 => 'String',
            1 => '"%B %d, %Y"',
            2 => 25,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        66 => [
            0 => 'TextData',
            1 => '
      </div>
    </li>
  ',
            2 => 25,
        ],
        67 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 28,
        ],
        69 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        70 => [
            0 => 'TextData',
            1 => '
  </ul>

  <!-- Comment Form -->
  <div id="comment-form">
  ',
            2 => 28,
        ],
        71 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 33,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 33,
        ],
        74 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        75 => [
            0 => 'TextData',
            1 => '
    <h3>Leave a comment</h3>

    <!-- Check if a comment has been submitted in the last request, and if yes display an appropriate message -->
    ',
            2 => 33,
        ],
        76 => [
            0 => 'BlockStart',
            1 => '',
            2 => 37,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 37,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 37,
        ],
        79 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'posted_successfully?',
            2 => 37,
        ],
        81 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 37,
        ],
        82 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 37,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 38,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 38,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 38,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        89 => [
            0 => 'TextData',
            1 => '
        <div class="notice">
          Successfully posted your comment.<br />
          It will have to be approved by the blog owner first before showing up.
        </div>
      ',
            2 => 38,
        ],
        90 => [
            0 => 'BlockStart',
            1 => '',
            2 => 43,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 43,
        ],
        92 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 43,
        ],
        93 => [
            0 => 'TextData',
            1 => '
        <div class="notice">Successfully posted your comment.</div>
      ',
            2 => 43,
        ],
        94 => [
            0 => 'BlockStart',
            1 => '',
            2 => 45,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 45,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 45,
        ],
        97 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 45,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 46,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        101 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 46,
        ],
        102 => [
            0 => 'BlockStart',
            1 => '',
            2 => 48,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 48,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 48,
        ],
        105 => [
            0 => 'Dot',
            1 => '.',
            2 => 48,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 48,
        ],
        107 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 48,
        ],
        108 => [
            0 => 'TextData',
            1 => '
      <div class="notice error">Not all the fields have been filled out correctly!</div>
    ',
            2 => 48,
        ],
        109 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 50,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        112 => [
            0 => 'TextData',
            1 => '

    <dl>
      <dt class="',
            2 => 50,
        ],
        113 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 53,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 53,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 53,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 53,
        ],
        118 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 53,
        ],
        119 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 53,
        ],
        120 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        121 => [
            0 => 'TextData',
            1 => 'error',
            2 => 53,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 53,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        125 => [
            0 => 'TextData',
            1 => '"><label for="comment_author">Your name</label></dt>
      <dd><input type="text" id="comment_author" name="comment[author]" size="40" value="',
            2 => 53,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 54,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 54,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 54,
        ],
        130 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 54,
        ],
        131 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 54,
        ],
        132 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 54,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 54,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 54,
        ],
        137 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 54,
        ],
        138 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 54,
        ],
        139 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        140 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 54,
        ],
        141 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 54,
        ],
        143 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        144 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 54,
        ],
        145 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 56,
        ],
        148 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 56,
        ],
        150 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 56,
        ],
        151 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 56,
        ],
        152 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        153 => [
            0 => 'TextData',
            1 => 'error',
            2 => 56,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        157 => [
            0 => 'TextData',
            1 => '"><label for="comment_email">Your email</label></dt>
      <dd><input type="text" id="comment_email" name="comment[email]" size="40" value="',
            2 => 56,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 57,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 57,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 57,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'email',
            2 => 57,
        ],
        162 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 57,
        ],
        163 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 57,
        ],
        164 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 57,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 57,
        ],
        167 => [
            0 => 'Dot',
            1 => '.',
            2 => 57,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 57,
        ],
        169 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 57,
        ],
        170 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 57,
        ],
        171 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        172 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 57,
        ],
        173 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 57,
        ],
        175 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        176 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 57,
        ],
        177 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        178 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 59,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 59,
        ],
        180 => [
            0 => 'Dot',
            1 => '.',
            2 => 59,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 59,
        ],
        182 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 59,
        ],
        183 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 59,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        185 => [
            0 => 'TextData',
            1 => 'error',
            2 => 59,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 59,
        ],
        188 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        189 => [
            0 => 'TextData',
            1 => '"><label for="comment_body">Your comment</label></dt>
      <dd><textarea id="comment_body" name="comment[body]" cols="40" rows="5" class="',
            2 => 59,
        ],
        190 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 60,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 60,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 60,
        ],
        195 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 60,
        ],
        196 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 60,
        ],
        197 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        198 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 60,
        ],
        199 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 60,
        ],
        201 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        202 => [
            0 => 'TextData',
            1 => '">',
            2 => 60,
        ],
        203 => [
            0 => 'VariableStart',
            1 => '',
            2 => 60,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 60,
        ],
        205 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'body',
            2 => 60,
        ],
        207 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 60,
        ],
        208 => [
            0 => 'TextData',
            1 => '</textarea></dd>
    </dl>

    ',
            2 => 60,
        ],
        209 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        210 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 63,
        ],
        211 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 63,
        ],
        212 => [
            0 => 'Dot',
            1 => '.',
            2 => 63,
        ],
        213 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 63,
        ],
        214 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        215 => [
            0 => 'TextData',
            1 => '
      <p class="hint">comments have to be approved before showing up</p>
    ',
            2 => 63,
        ],
        216 => [
            0 => 'BlockStart',
            1 => '',
            2 => 65,
        ],
        217 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 65,
        ],
        218 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 65,
        ],
        219 => [
            0 => 'TextData',
            1 => '

    <input type="submit" value="Post comment" id="comment-submit" />
  ',
            2 => 65,
        ],
        220 => [
            0 => 'BlockStart',
            1 => '',
            2 => 68,
        ],
        221 => [
            0 => 'Identifier',
            1 => 'endform',
            2 => 68,
        ],
        222 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 68,
        ],
        223 => [
            0 => 'TextData',
            1 => '
  </div>
  <!-- END Comment Form -->

</div>
',
            2 => 68,
        ],
        224 => [
            0 => 'BlockStart',
            1 => '',
            2 => 73,
        ],
        225 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 73,
        ],
        226 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 73,
        ],
        227 => [
            0 => 'TextData',
            1 => '
<!-- END Comments -->
',
            2 => 73,
        ],
    ],
    'performance/tests/ripen/blog.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="blog-page">
  <h2 class="heading-shaded">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>
   ',
            2 => 2,
        ],
        7 => [
            0 => 'BlockStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 3,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 3,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 3,
        ],
        12 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 3,
        ],
        14 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 3,
        ],
        15 => [
            0 => 'TextData',
            1 => '
      <h4>
        ',
            2 => 3,
        ],
        16 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 5,
        ],
        18 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 5,
        ],
        20 => [
            0 => 'Pipe',
            1 => '|',
            2 => 5,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 5,
        ],
        22 => [
            0 => 'Colon',
            1 => ':',
            2 => 5,
        ],
        23 => [
            0 => 'String',
            1 => '\'%d %b\'',
            2 => 5,
        ],
        24 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        25 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 5,
        ],
        26 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        28 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 6,
        ],
        30 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        31 => [
            0 => 'TextData',
            1 => '">',
            2 => 6,
        ],
        32 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        34 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 6,
        ],
        36 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        37 => [
            0 => 'TextData',
            1 => '</a>
      </h4>
      ',
            2 => 6,
        ],
        38 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 8,
        ],
        40 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 8,
        ],
        42 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        43 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 8,
        ],
        44 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 9,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 9,
        ],
        47 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 9,
        ],
        49 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        50 => [
            0 => 'TextData',
            1 => '
        <p><a href="',
            2 => 9,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 10,
        ],
        53 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 10,
        ],
        55 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        56 => [
            0 => 'TextData',
            1 => '#comments">',
            2 => 10,
        ],
        57 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 10,
        ],
        59 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'comments_count',
            2 => 10,
        ],
        61 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        62 => [
            0 => 'TextData',
            1 => ' comments</a></p>
      ',
            2 => 10,
        ],
        63 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 11,
        ],
        65 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        66 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 11,
        ],
        67 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 12,
        ],
        69 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        70 => [
            0 => 'TextData',
            1 => '
</div>
',
            2 => 12,
        ],
    ],
    'performance/tests/ripen/cart.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<script type="text/javascript">
  function remove_item(id) {
      document.getElementById(\'updates_\'+id).value = 0;
      document.getElementById(\'cart\').submit();
  }
</script>

<div id="cart-page">

  ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 10,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 10,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 10,
        ],
        6 => [
            0 => 'Comparison',
            1 => '==',
            2 => 10,
        ],
        7 => [
            0 => 'Number',
            1 => '0',
            2 => 10,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        9 => [
            0 => 'TextData',
            1 => '
    <p>Your shopping cart is empty...</p>
  <p><a href="/"><img src="',
            2 => 10,
        ],
        10 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        11 => [
            0 => 'String',
            1 => '\'continue_shopping_icon.gif\'',
            2 => 12,
        ],
        12 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 12,
        ],
        14 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        15 => [
            0 => 'TextData',
            1 => '" alt="Continue shopping"/></a><p>
  ',
            2 => 12,
        ],
        16 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 13,
        ],
        18 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        19 => [
            0 => 'TextData',
            1 => '

  <form action="/cart" method="post" id="cart">

  <table class="cart">
      <tr>
        <th colspan="2">Product</th>
        <th class="short">Qty</th>
        <th>Price</th>
        <th>Total</th>
        <th class="short">Remove</th>
      </tr>

      ',
            2 => 13,
        ],
        20 => [
            0 => 'BlockStart',
            1 => '',
            2 => 26,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 26,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 26,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 26,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 26,
        ],
        25 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'items',
            2 => 26,
        ],
        27 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 26,
        ],
        28 => [
            0 => 'TextData',
            1 => '
      <tr class="',
            2 => 26,
        ],
        29 => [
            0 => 'BlockStart',
            1 => '',
            2 => 27,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'cycle',
            2 => 27,
        ],
        31 => [
            0 => 'String',
            1 => '\'odd\'',
            2 => 27,
        ],
        32 => [
            0 => 'Comma',
            1 => ',',
            2 => 27,
        ],
        33 => [
            0 => 'String',
            1 => '\'even\'',
            2 => 27,
        ],
        34 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 27,
        ],
        35 => [
            0 => 'TextData',
            1 => '">
        <td class="short">',
            2 => 27,
        ],
        36 => [
            0 => 'VariableStart',
            1 => '',
            2 => 28,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 28,
        ],
        38 => [
            0 => 'Dot',
            1 => '.',
            2 => 28,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 28,
        ],
        40 => [
            0 => 'Dot',
            1 => '.',
            2 => 28,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 28,
        ],
        42 => [
            0 => 'Pipe',
            1 => '|',
            2 => 28,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 28,
        ],
        44 => [
            0 => 'Colon',
            1 => ':',
            2 => 28,
        ],
        45 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 28,
        ],
        46 => [
            0 => 'Pipe',
            1 => '|',
            2 => 28,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'img_tag',
            2 => 28,
        ],
        48 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 28,
        ],
        49 => [
            0 => 'TextData',
            1 => '</td>
    <td><a href="',
            2 => 28,
        ],
        50 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 29,
        ],
        52 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 29,
        ],
        54 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 29,
        ],
        56 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        57 => [
            0 => 'TextData',
            1 => '">',
            2 => 29,
        ],
        58 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 29,
        ],
        60 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 29,
        ],
        62 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        63 => [
            0 => 'TextData',
            1 => '</a></td>
        <td class="short"><input type="text" class="quantity" name="updates[',
            2 => 29,
        ],
        64 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 30,
        ],
        66 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 30,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 30,
        ],
        70 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        71 => [
            0 => 'TextData',
            1 => ']" id="updates_',
            2 => 30,
        ],
        72 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 30,
        ],
        74 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 30,
        ],
        76 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 30,
        ],
        78 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        79 => [
            0 => 'TextData',
            1 => '" value="',
            2 => 30,
        ],
        80 => [
            0 => 'VariableStart',
            1 => '',
            2 => 30,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 30,
        ],
        82 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'quantity',
            2 => 30,
        ],
        84 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 30,
        ],
        85 => [
            0 => 'TextData',
            1 => '" onfocus="this.select();"/></td>
        <td class="cart-price">',
            2 => 30,
        ],
        86 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 31,
        ],
        88 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 31,
        ],
        90 => [
            0 => 'Pipe',
            1 => '|',
            2 => 31,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 31,
        ],
        92 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        93 => [
            0 => 'TextData',
            1 => '</td>
        <td class="cart-price">',
            2 => 31,
        ],
        94 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 32,
        ],
        96 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'line_price',
            2 => 32,
        ],
        98 => [
            0 => 'Pipe',
            1 => '|',
            2 => 32,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 32,
        ],
        100 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        101 => [
            0 => 'TextData',
            1 => '</td>
        <td class="short"><a href="#" onclick="remove_item(',
            2 => 32,
        ],
        102 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        104 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 33,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 33,
        ],
        108 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        109 => [
            0 => 'TextData',
            1 => '); return false;" class="remove"><img src="',
            2 => 33,
        ],
        110 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        111 => [
            0 => 'String',
            1 => '\'cancel_icon.gif\'',
            2 => 33,
        ],
        112 => [
            0 => 'Pipe',
            1 => '|',
            2 => 33,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 33,
        ],
        114 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        115 => [
            0 => 'TextData',
            1 => '" alt="Remove" /></a></td>
      </tr>
      ',
            2 => 33,
        ],
        116 => [
            0 => 'BlockStart',
            1 => '',
            2 => 35,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 35,
        ],
        118 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 35,
        ],
        119 => [
            0 => 'TextData',
            1 => '
    </table>
    <p class="updatebtn"><input type="image" value="Update Cart" name="update" src="',
            2 => 35,
        ],
        120 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        121 => [
            0 => 'String',
            1 => '\'update_icon.gif\'',
            2 => 37,
        ],
        122 => [
            0 => 'Pipe',
            1 => '|',
            2 => 37,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 37,
        ],
        124 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        125 => [
            0 => 'TextData',
            1 => '" alt="Update" /></p>
    <p class="subtotal">
    <strong>Subtotal:</strong> ',
            2 => 37,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 39,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 39,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 39,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 39,
        ],
        130 => [
            0 => 'Pipe',
            1 => '|',
            2 => 39,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 39,
        ],
        132 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 39,
        ],
        133 => [
            0 => 'TextData',
            1 => '
    </p>
    <p class="checkout"><input type="image" src="',
            2 => 39,
        ],
        134 => [
            0 => 'VariableStart',
            1 => '',
            2 => 41,
        ],
        135 => [
            0 => 'String',
            1 => '\'checkout_icon.gif\'',
            2 => 41,
        ],
        136 => [
            0 => 'Pipe',
            1 => '|',
            2 => 41,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 41,
        ],
        138 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 41,
        ],
        139 => [
            0 => 'TextData',
            1 => '" alt="Proceed to Checkout" value="Proceed to Checkout" name="checkout"  /></p>

    ',
            2 => 41,
        ],
        140 => [
            0 => 'BlockStart',
            1 => '',
            2 => 43,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 43,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'additional_checkout_buttons',
            2 => 43,
        ],
        143 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 43,
        ],
        144 => [
            0 => 'TextData',
            1 => '
    <div class="additional-checkout-buttons">
      <p>- or -</p>
      ',
            2 => 43,
        ],
        145 => [
            0 => 'VariableStart',
            1 => '',
            2 => 46,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'content_for_additional_checkout_buttons',
            2 => 46,
        ],
        147 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 46,
        ],
        148 => [
            0 => 'TextData',
            1 => '
    </div>
    ',
            2 => 46,
        ],
        149 => [
            0 => 'BlockStart',
            1 => '',
            2 => 48,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 48,
        ],
        151 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 48,
        ],
        152 => [
            0 => 'TextData',
            1 => '

  </form>

  ',
            2 => 48,
        ],
        153 => [
            0 => 'BlockStart',
            1 => '',
            2 => 52,
        ],
        154 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 52,
        ],
        155 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 52,
        ],
        156 => [
            0 => 'TextData',
            1 => '

</div>
',
            2 => 52,
        ],
    ],
    'performance/tests/ripen/collection.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="collection-page">

',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 3,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 3,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 3,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 3,
        ],
        6 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 3,
        ],
        7 => [
            0 => 'TextData',
            1 => '
  <div id="collection-description" class="textile">',
            2 => 3,
        ],
        8 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 4,
        ],
        10 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 4,
        ],
        12 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        13 => [
            0 => 'TextData',
            1 => '</div>
',
            2 => 4,
        ],
        14 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 5,
        ],
        16 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        17 => [
            0 => 'TextData',
            1 => '

',
            2 => 5,
        ],
        18 => [
            0 => 'BlockStart',
            1 => '',
            2 => 7,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 7,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 7,
        ],
        21 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 7,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 7,
        ],
        24 => [
            0 => 'Number',
            1 => '20',
            2 => 7,
        ],
        25 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 7,
        ],
        26 => [
            0 => 'TextData',
            1 => '

<ul id="product-collection">
    ',
            2 => 7,
        ],
        27 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 10,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 10,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 10,
        ],
        32 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 10,
        ],
        34 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        35 => [
            0 => 'TextData',
            1 => '
    <li class="single-product clearfix">
      <div class="small">
        <div class="prod-image"><a href="',
            2 => 10,
        ],
        36 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        38 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 13,
        ],
        40 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        41 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 13,
        ],
        42 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        44 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 13,
        ],
        46 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 13,
        ],
        48 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        49 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 13,
        ],
        50 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        51 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 13,
        ],
        52 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        54 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 13,
        ],
        56 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 13,
        ],
        58 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        59 => [
            0 => 'TextData',
            1 => '" /></a></div>
      </div>
      <div class="prod-list-description">
        <h3><a href="',
            2 => 13,
        ],
        60 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        62 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 16,
        ],
        64 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        65 => [
            0 => 'TextData',
            1 => '">',
            2 => 16,
        ],
        66 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 16,
        ],
        70 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        71 => [
            0 => 'TextData',
            1 => '</a></h3>
        <p>',
            2 => 16,
        ],
        72 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 17,
        ],
        74 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 17,
        ],
        76 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 17,
        ],
        78 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 17,
        ],
        80 => [
            0 => 'Colon',
            1 => ':',
            2 => 17,
        ],
        81 => [
            0 => 'Number',
            1 => '35',
            2 => 17,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        83 => [
            0 => 'TextData',
            1 => '</p>
      <p class="prd-price">',
            2 => 17,
        ],
        84 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 18,
        ],
        88 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 18,
        ],
        90 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        91 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 18,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        94 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'price_varies',
            2 => 18,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        97 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 18,
        ],
        98 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        100 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'price_max',
            2 => 18,
        ],
        102 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 18,
        ],
        104 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        105 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 18,
        ],
        107 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        108 => [
            0 => 'TextData',
            1 => '</p>
     </div>
    </li>
    ',
            2 => 18,
        ],
        109 => [
            0 => 'BlockStart',
            1 => '',
            2 => 21,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 21,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 21,
        ],
        112 => [
            0 => 'TextData',
            1 => '
</ul>

<div id="pagination">
  ',
            2 => 21,
        ],
        113 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 25,
        ],
        115 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 25,
        ],
        117 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        118 => [
            0 => 'TextData',
            1 => '
</div>

',
            2 => 25,
        ],
        119 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        120 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 28,
        ],
        121 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        122 => [
            0 => 'TextData',
            1 => '
</div>
',
            2 => 28,
        ],
    ],
    'performance/tests/ripen/index.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="home-page">
  <h3 class="heading-shaded">Featured products...</h3>
  <div class="featured-prod-row clearfix">
    ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 4,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 4,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 4,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 4,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 4,
        ],
        8 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 4,
        ],
        10 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        11 => [
            0 => 'TextData',
            1 => '
      <div class="featured-prod-item">
          <p>
        <a href="',
            2 => 4,
        ],
        12 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        14 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 7,
        ],
        16 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        17 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 7,
        ],
        18 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 7,
        ],
        22 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 7,
        ],
        24 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        25 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 7,
        ],
        26 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        27 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 7,
        ],
        28 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        30 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 7,
        ],
        32 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 7,
        ],
        34 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        35 => [
            0 => 'TextData',
            1 => '"/></a>
        </p>
        <h4><a href="',
            2 => 7,
        ],
        36 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 9,
        ],
        38 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 9,
        ],
        40 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        41 => [
            0 => 'TextData',
            1 => '">',
            2 => 9,
        ],
        42 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 9,
        ],
        44 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 9,
        ],
        46 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        47 => [
            0 => 'TextData',
            1 => '</a></h4>
        ',
            2 => 9,
        ],
        48 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 10,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        51 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 10,
        ],
        53 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        54 => [
            0 => 'TextData',
            1 => '
          ',
            2 => 10,
        ],
        55 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 11,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        58 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 11,
        ],
        60 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 11,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        62 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 11,
        ],
        64 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        65 => [
            0 => 'TextData',
            1 => '
            <p class="prd-price">Was:<del>',
            2 => 11,
        ],
        66 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 12,
        ],
        70 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 12,
        ],
        72 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        73 => [
            0 => 'TextData',
            1 => '</del></p>
            <p class="prd-price"><ins>Now: ',
            2 => 12,
        ],
        74 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        76 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 13,
        ],
        78 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 13,
        ],
        80 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        81 => [
            0 => 'TextData',
            1 => '</ins></p>
          ',
            2 => 13,
        ],
        82 => [
            0 => 'BlockStart',
            1 => '',
            2 => 14,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 14,
        ],
        84 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 14,
        ],
        85 => [
            0 => 'TextData',
            1 => '
        ',
            2 => 14,
        ],
        86 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 15,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        89 => [
            0 => 'TextData',
            1 => '
          <p class="prd-price"><ins>',
            2 => 15,
        ],
        90 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        92 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 16,
        ],
        94 => [
            0 => 'Pipe',
            1 => '|',
            2 => 16,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 16,
        ],
        96 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        97 => [
            0 => 'TextData',
            1 => '</ins></p>
        ',
            2 => 16,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 17,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 17,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 17,
        ],
        101 => [
            0 => 'TextData',
            1 => '
      </div>
    ',
            2 => 17,
        ],
        102 => [
            0 => 'BlockStart',
            1 => '',
            2 => 19,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 19,
        ],
        104 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 19,
        ],
        105 => [
            0 => 'TextData',
            1 => '
  </div>

  <div id="articles">
    ',
            2 => 19,
        ],
        106 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'assign',
            2 => 23,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 23,
        ],
        109 => [
            0 => 'Equals',
            1 => '=',
            2 => 23,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 23,
        ],
        111 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        112 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 23,
        ],
        113 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        114 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 23,
        ],
        115 => [
            0 => 'BlockStart',
            1 => '',
            2 => 24,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 24,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 24,
        ],
        118 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 24,
        ],
        120 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 24,
        ],
        121 => [
            0 => 'String',
            1 => '""',
            2 => 24,
        ],
        122 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 24,
        ],
        123 => [
            0 => 'TextData',
            1 => '
      <h3>',
            2 => 24,
        ],
        124 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 25,
        ],
        126 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 25,
        ],
        128 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        129 => [
            0 => 'TextData',
            1 => '</h3>
      ',
            2 => 25,
        ],
        130 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 26,
        ],
        132 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 26,
        ],
        134 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        135 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 26,
        ],
        136 => [
            0 => 'BlockStart',
            1 => '',
            2 => 27,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 27,
        ],
        138 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 27,
        ],
        139 => [
            0 => 'TextData',
            1 => '
      In <em>Admin &gt; Blogs &amp; Pages</em>, create a page with the handle <strong><code>frontpage</code></strong> and it will show up here.<br />
      ',
            2 => 27,
        ],
        140 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        141 => [
            0 => 'String',
            1 => '"Learn more about handles"',
            2 => 29,
        ],
        142 => [
            0 => 'Pipe',
            1 => '|',
            2 => 29,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 29,
        ],
        144 => [
            0 => 'Colon',
            1 => ':',
            2 => 29,
        ],
        145 => [
            0 => 'String',
            1 => '"http://wiki.shopify.com/Handle"',
            2 => 29,
        ],
        146 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        147 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 29,
        ],
        148 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 30,
        ],
        150 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        151 => [
            0 => 'TextData',
            1 => '
  </div>
</div>
',
            2 => 30,
        ],
    ],
    'performance/tests/ripen/page.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="single-page">
<h2 class="heading-shaded">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>
  ',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 3,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 3,
        ],
        11 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        12 => [
            0 => 'TextData',
            1 => '
</div>
',
            2 => 3,
        ],
    ],
    'performance/tests/ripen/product.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="product-page">
  <h2 class="heading-shaded">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h2>
  <div id="product-details">
  <div id="product-images">
    ',
            2 => 2,
        ],
        7 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 5,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 5,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 5,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 5,
        ],
        12 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 5,
        ],
        14 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        15 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 5,
        ],
        16 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 6,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 6,
        ],
        19 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 6,
        ],
        21 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        22 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 6,
        ],
        23 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 7,
        ],
        25 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 7,
        ],
        27 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        28 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 7,
        ],
        29 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        30 => [
            0 => 'TextData',
            1 => '" class="product-image" rel="lightbox[ product]" title="">
          <img src="',
            2 => 7,
        ],
        31 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 8,
        ],
        33 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 8,
        ],
        35 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        36 => [
            0 => 'String',
            1 => '\'medium\'',
            2 => 8,
        ],
        37 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        38 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 8,
        ],
        39 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 8,
        ],
        43 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 8,
        ],
        45 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        46 => [
            0 => 'TextData',
            1 => '" />
        </a>
      ',
            2 => 8,
        ],
        47 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 10,
        ],
        49 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        50 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 10,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 11,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 11,
        ],
        55 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        56 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 11,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        58 => [
            0 => 'TextData',
            1 => '" class="product-image-small" rel="lightbox[ product]" title="">
          <img src="',
            2 => 11,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 12,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 12,
        ],
        63 => [
            0 => 'Colon',
            1 => ':',
            2 => 12,
        ],
        64 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 12,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        66 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 12,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        69 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 12,
        ],
        71 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 12,
        ],
        73 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        74 => [
            0 => 'TextData',
            1 => '" />
        </a>
      ',
            2 => 12,
        ],
        75 => [
            0 => 'BlockStart',
            1 => '',
            2 => 14,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 14,
        ],
        77 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 14,
        ],
        78 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 14,
        ],
        79 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 15,
        ],
        81 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        82 => [
            0 => 'TextData',
            1 => '
  </div>

  <ul id="product-info">
    <li>Vendor: ',
            2 => 15,
        ],
        83 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        85 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'vendor',
            2 => 19,
        ],
        87 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'link_to_vendor',
            2 => 19,
        ],
        89 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        90 => [
            0 => 'TextData',
            1 => '</li>
    <li>Type: ',
            2 => 19,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 20,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 20,
        ],
        93 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'type',
            2 => 20,
        ],
        95 => [
            0 => 'Pipe',
            1 => '|',
            2 => 20,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'link_to_type',
            2 => 20,
        ],
        97 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 20,
        ],
        98 => [
            0 => 'TextData',
            1 => '</li>
    </ul>

    <small>',
            2 => 20,
        ],
        99 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        101 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 23,
        ],
        103 => [
            0 => 'Pipe',
            1 => '|',
            2 => 23,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 23,
        ],
        105 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        106 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 23,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        109 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'price_varies',
            2 => 23,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        112 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 23,
        ],
        113 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        115 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'price_max',
            2 => 23,
        ],
        117 => [
            0 => 'Pipe',
            1 => '|',
            2 => 23,
        ],
        118 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 23,
        ],
        119 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        120 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 23,
        ],
        122 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        123 => [
            0 => 'TextData',
            1 => '</small>

    <div id="product-options">
              ',
            2 => 23,
        ],
        124 => [
            0 => 'BlockStart',
            1 => '',
            2 => 26,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 26,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 26,
        ],
        127 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'available',
            2 => 26,
        ],
        129 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 26,
        ],
        130 => [
            0 => 'TextData',
            1 => '

    <form action="/cart/add" method="post">

      <select id="product-select" name=\'id\'>
        ',
            2 => 26,
        ],
        131 => [
            0 => 'BlockStart',
            1 => '',
            2 => 31,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 31,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 31,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 31,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 31,
        ],
        136 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 31,
        ],
        138 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 31,
        ],
        139 => [
            0 => 'TextData',
            1 => '
          <option value="',
            2 => 31,
        ],
        140 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 32,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 32,
        ],
        144 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        145 => [
            0 => 'TextData',
            1 => '">',
            2 => 32,
        ],
        146 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 32,
        ],
        148 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 32,
        ],
        150 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        151 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 32,
        ],
        152 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 32,
        ],
        154 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 32,
        ],
        156 => [
            0 => 'Pipe',
            1 => '|',
            2 => 32,
        ],
        157 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 32,
        ],
        158 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        159 => [
            0 => 'TextData',
            1 => '</option>
        ',
            2 => 32,
        ],
        160 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 33,
        ],
        162 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        163 => [
            0 => 'TextData',
            1 => '
      </select>

      <div id="price-field"></div>

      <div class="add-to-cart"><input type="image" name="add" value="Add to Cart" id="add" src="',
            2 => 33,
        ],
        164 => [
            0 => 'VariableStart',
            1 => '',
            2 => 38,
        ],
        165 => [
            0 => 'String',
            1 => '\'add-to-cart.gif\'',
            2 => 38,
        ],
        166 => [
            0 => 'Pipe',
            1 => '|',
            2 => 38,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 38,
        ],
        168 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 38,
        ],
        169 => [
            0 => 'TextData',
            1 => '" /></div>
    </form>
              ',
            2 => 38,
        ],
        170 => [
            0 => 'BlockStart',
            1 => '',
            2 => 40,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 40,
        ],
        172 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 40,
        ],
        173 => [
            0 => 'TextData',
            1 => '
                  <span>Sold Out!</span>
              ',
            2 => 40,
        ],
        174 => [
            0 => 'BlockStart',
            1 => '',
            2 => 42,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 42,
        ],
        176 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 42,
        ],
        177 => [
            0 => 'TextData',
            1 => '
    </div>

    <div class="product-description">
    ',
            2 => 42,
        ],
        178 => [
            0 => 'VariableStart',
            1 => '',
            2 => 46,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 46,
        ],
        180 => [
            0 => 'Dot',
            1 => '.',
            2 => 46,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 46,
        ],
        182 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 46,
        ],
        183 => [
            0 => 'TextData',
            1 => '
    </div>
  </div>
</div>


<script type="text/javascript">
<!--
  // mootools callback for multi variants dropdown selector
  var selectCallback = function(variant, selector) {
    if (variant && variant.available == true) {
      // selected a valid variant
      $(\'add\').removeClass(\'disabled\'); // remove unavailable class from add-to-cart button
      $(\'add\').disabled = false;           // reenable add-to-cart button
      $(\'price-field\').innerHTML = Shopify.formatMoney(variant.price, "',
            2 => 46,
        ],
        184 => [
            0 => 'VariableStart',
            1 => '',
            2 => 60,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 60,
        ],
        186 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'money_with_currency_format',
            2 => 60,
        ],
        188 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 60,
        ],
        189 => [
            0 => 'TextData',
            1 => '");  // update price field
    } else {
      // variant doesn\'t exist
      $(\'add\').addClass(\'disabled\');      // set add-to-cart button to unavailable class
      $(\'add\').disabled = true;              // disable add-to-cart button
      $(\'price-field\').innerHTML = (variant) ? "Sold Out" : "Unavailable"; // update price-field message
    }
  };

  // initialize multi selector for product
  window.addEvent(\'domready\', function() {
    new Shopify.OptionSelectors("product-select", { product: ',
            2 => 60,
        ],
        190 => [
            0 => 'VariableStart',
            1 => '',
            2 => 71,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 71,
        ],
        192 => [
            0 => 'Pipe',
            1 => '|',
            2 => 71,
        ],
        193 => [
            0 => 'Identifier',
            1 => 'json',
            2 => 71,
        ],
        194 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 71,
        ],
        195 => [
            0 => 'TextData',
            1 => ', onVariantSelected: selectCallback });
  });
-->
</script>

',
            2 => 71,
        ],
    ],
    'performance/tests/ripen/theme.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
  <title>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 4,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 4,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        6 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 4,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page_title',
            2 => 4,
        ],
        9 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        10 => [
            0 => 'TextData',
            1 => '</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  ',
            2 => 4,
        ],
        11 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        12 => [
            0 => 'String',
            1 => '\'main.css\'',
            2 => 7,
        ],
        13 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 7,
        ],
        15 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 7,
        ],
        17 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        18 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 7,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        20 => [
            0 => 'String',
            1 => '\'shop.js\'',
            2 => 8,
        ],
        21 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 8,
        ],
        23 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 8,
        ],
        25 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        26 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 8,
        ],
        27 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        28 => [
            0 => 'String',
            1 => '\'mootools.js\'',
            2 => 10,
        ],
        29 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 10,
        ],
        31 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 10,
        ],
        33 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        34 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 10,
        ],
        35 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        36 => [
            0 => 'String',
            1 => '\'slimbox.js\'',
            2 => 11,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 11,
        ],
        39 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 11,
        ],
        41 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        42 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 11,
        ],
        43 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        44 => [
            0 => 'String',
            1 => '\'option_selection.js\'',
            2 => 12,
        ],
        45 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'shopify_asset_url',
            2 => 12,
        ],
        47 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 12,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        50 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 12,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        52 => [
            0 => 'String',
            1 => '\'slimbox.css\'',
            2 => 13,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 13,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 13,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        58 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 13,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'content_for_header',
            2 => 15,
        ],
        61 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        62 => [
            0 => 'TextData',
            1 => '
 </head>

<body id="page-',
            2 => 15,
        ],
        63 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 18,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        66 => [
            0 => 'TextData',
            1 => '">
<p class="hide"><a href="#navigation">Skip to navigation.</a></p>
<div id="wrapper">
  <div class="content clearfix">
    <div id="header">
      <h2><a href="/">',
            2 => 18,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 23,
        ],
        69 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 23,
        ],
        71 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        72 => [
            0 => 'TextData',
            1 => '</a></h2>
    </div>
    <div id="left-col">
      ',
            2 => 23,
        ],
        73 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'content_for_layout',
            2 => 26,
        ],
        75 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        76 => [
            0 => 'TextData',
            1 => '
    </div>
    <div id="right-col">
      ',
            2 => 26,
        ],
        77 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 29,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 29,
        ],
        80 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 29,
        ],
        81 => [
            0 => 'String',
            1 => '\'cart\'',
            2 => 29,
        ],
        82 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        83 => [
            0 => 'TextData',
            1 => '
          <div id="cart-right-col">
          <dl id="cart-right-col-info">
            <dt>Shopping Cart</dt>
            <dd>
            ',
            2 => 29,
        ],
        84 => [
            0 => 'BlockStart',
            1 => '',
            2 => 34,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 34,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 34,
        ],
        87 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 34,
        ],
        89 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 34,
        ],
        90 => [
            0 => 'Number',
            1 => '0',
            2 => 34,
        ],
        91 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 34,
        ],
        92 => [
            0 => 'TextData',
            1 => '
              <a href="/cart">',
            2 => 34,
        ],
        93 => [
            0 => 'VariableStart',
            1 => '',
            2 => 35,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 35,
        ],
        95 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 35,
        ],
        97 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 35,
        ],
        98 => [
            0 => 'TextData',
            1 => ' ',
            2 => 35,
        ],
        99 => [
            0 => 'VariableStart',
            1 => '',
            2 => 35,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 35,
        ],
        101 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 35,
        ],
        103 => [
            0 => 'Pipe',
            1 => '|',
            2 => 35,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 35,
        ],
        105 => [
            0 => 'Colon',
            1 => ':',
            2 => 35,
        ],
        106 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 35,
        ],
        107 => [
            0 => 'Comma',
            1 => ',',
            2 => 35,
        ],
        108 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 35,
        ],
        109 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 35,
        ],
        110 => [
            0 => 'TextData',
            1 => '</a> in your cart
            ',
            2 => 35,
        ],
        111 => [
            0 => 'BlockStart',
            1 => '',
            2 => 36,
        ],
        112 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 36,
        ],
        113 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 36,
        ],
        114 => [
            0 => 'TextData',
            1 => '
              Your cart is empty
            ',
            2 => 36,
        ],
        115 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 38,
        ],
        117 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        118 => [
            0 => 'TextData',
            1 => '
            </dd>
          </dl>
        </div>
      ',
            2 => 38,
        ],
        119 => [
            0 => 'BlockStart',
            1 => '',
            2 => 42,
        ],
        120 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 42,
        ],
        121 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 42,
        ],
        122 => [
            0 => 'TextData',
            1 => '
      <div id="search">
        <dl id="searchbox">
        <dt>Search</dt>
        <dd>
        <form action="/search" method="get">
        <fieldset>
        <input class="search-input" type="text" onclick="this.select()" value="Search this shop..." name="q" />
        </fieldset>
        </form>
        </dd>
        </dl>
      </div>
      <div id="navigation">
        <dl class="navbar">
        <dt>Navigation</dt>
        ',
            2 => 42,
        ],
        123 => [
            0 => 'BlockStart',
            1 => '',
            2 => 58,
        ],
        124 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 58,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 58,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 58,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 58,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 58,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 58,
        ],
        130 => [
            0 => 'Dot',
            1 => '.',
            2 => 58,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 58,
        ],
        132 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 58,
        ],
        133 => [
            0 => 'TextData',
            1 => '
          <dd>',
            2 => 58,
        ],
        134 => [
            0 => 'VariableStart',
            1 => '',
            2 => 59,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 59,
        ],
        136 => [
            0 => 'Dot',
            1 => '.',
            2 => 59,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 59,
        ],
        138 => [
            0 => 'Pipe',
            1 => '|',
            2 => 59,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 59,
        ],
        140 => [
            0 => 'Colon',
            1 => ':',
            2 => 59,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 59,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 59,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 59,
        ],
        144 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 59,
        ],
        145 => [
            0 => 'TextData',
            1 => '</dd>
        ',
            2 => 59,
        ],
        146 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 60,
        ],
        148 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        149 => [
            0 => 'TextData',
            1 => '
        </dl>

        ',
            2 => 60,
        ],
        150 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 63,
        ],
        152 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 63,
        ],
        153 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        154 => [
            0 => 'TextData',
            1 => '
        <dl class="navbar">
        <dt>Tags</dt>
          ',
            2 => 63,
        ],
        155 => [
            0 => 'BlockStart',
            1 => '',
            2 => 66,
        ],
        156 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 66,
        ],
        157 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 66,
        ],
        158 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 66,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 66,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 66,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 66,
        ],
        162 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 66,
        ],
        163 => [
            0 => 'TextData',
            1 => '
          <dd>',
            2 => 66,
        ],
        164 => [
            0 => 'VariableStart',
            1 => '',
            2 => 67,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 67,
        ],
        166 => [
            0 => 'Pipe',
            1 => '|',
            2 => 67,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'highlight_active_tag',
            2 => 67,
        ],
        168 => [
            0 => 'Pipe',
            1 => '|',
            2 => 67,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'link_to_tag',
            2 => 67,
        ],
        170 => [
            0 => 'Colon',
            1 => ':',
            2 => 67,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 67,
        ],
        172 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 67,
        ],
        173 => [
            0 => 'TextData',
            1 => '</dd>
          ',
            2 => 67,
        ],
        174 => [
            0 => 'BlockStart',
            1 => '',
            2 => 68,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 68,
        ],
        176 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 68,
        ],
        177 => [
            0 => 'TextData',
            1 => '
          </dl>
        ',
            2 => 68,
        ],
        178 => [
            0 => 'BlockStart',
            1 => '',
            2 => 70,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 70,
        ],
        180 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 70,
        ],
        181 => [
            0 => 'TextData',
            1 => '
      </div>
    </div>

  </div>
    <div id="content-padding"></div>
</div>

<div id="footer">
  ',
            2 => 70,
        ],
        182 => [
            0 => 'BlockStart',
            1 => '',
            2 => 79,
        ],
        183 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 79,
        ],
        184 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 79,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 79,
        ],
        186 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 79,
        ],
        187 => [
            0 => 'Dot',
            1 => '.',
            2 => 79,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'footer',
            2 => 79,
        ],
        189 => [
            0 => 'Dot',
            1 => '.',
            2 => 79,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 79,
        ],
        191 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 79,
        ],
        192 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 79,
        ],
        193 => [
            0 => 'VariableStart',
            1 => '',
            2 => 80,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 80,
        ],
        195 => [
            0 => 'Dot',
            1 => '.',
            2 => 80,
        ],
        196 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 80,
        ],
        197 => [
            0 => 'Pipe',
            1 => '|',
            2 => 80,
        ],
        198 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 80,
        ],
        199 => [
            0 => 'Colon',
            1 => ':',
            2 => 80,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 80,
        ],
        201 => [
            0 => 'Dot',
            1 => '.',
            2 => 80,
        ],
        202 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 80,
        ],
        203 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 80,
        ],
        204 => [
            0 => 'TextData',
            1 => ' ',
            2 => 80,
        ],
        205 => [
            0 => 'BlockStart',
            1 => '',
            2 => 80,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 80,
        ],
        207 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 80,
        ],
        208 => [
            0 => 'Dot',
            1 => '.',
            2 => 80,
        ],
        209 => [
            0 => 'Identifier',
            1 => 'rindex',
            2 => 80,
        ],
        210 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 80,
        ],
        211 => [
            0 => 'Number',
            1 => '1',
            2 => 80,
        ],
        212 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 80,
        ],
        213 => [
            0 => 'TextData',
            1 => ' | ',
            2 => 80,
        ],
        214 => [
            0 => 'BlockStart',
            1 => '',
            2 => 80,
        ],
        215 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 80,
        ],
        216 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 80,
        ],
        217 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 80,
        ],
        218 => [
            0 => 'BlockStart',
            1 => '',
            2 => 81,
        ],
        219 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 81,
        ],
        220 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 81,
        ],
        221 => [
            0 => 'TextData',
            1 => '
</div>

</body>
</html>
',
            2 => 81,
        ],
    ],
    'performance/tests/tribble/404.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="page" class="innerpage clearfix">

    <div id="text-page">
      <div class="entry">
        <h1>Oh no!</h1>
        <div class="entry-post">
          Seems like you are looking for something that just isn\'t here. <a href="/">Try heading back to our main page</a>. Or you can checkout some of our featured products below.
        </div>
      </div>
    </div>


    <h1>Featured Products</h1>
    <ul class="item-list clearfix">

      ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 16,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 16,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 16,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 16,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 16,
        ],
        8 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 16,
        ],
        10 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 16,
        ],
        11 => [
            0 => 'TextData',
            1 => '
      <li>
        <form action="/cart/add" method="post">
        <div class="item-list-item">
          <div class="ili-top clearfix">
            <div class="ili-top-content">
              <h2><a href="',
            2 => 16,
        ],
        12 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 22,
        ],
        14 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 22,
        ],
        16 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        17 => [
            0 => 'TextData',
            1 => '">',
            2 => 22,
        ],
        18 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 22,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 22,
        ],
        22 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        23 => [
            0 => 'TextData',
            1 => '</a></h2>
              <p>',
            2 => 22,
        ],
        24 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 23,
        ],
        28 => [
            0 => 'Pipe',
            1 => '|',
            2 => 23,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 23,
        ],
        30 => [
            0 => 'Colon',
            1 => ':',
            2 => 23,
        ],
        31 => [
            0 => 'Number',
            1 => '15',
            2 => 23,
        ],
        32 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        33 => [
            0 => 'TextData',
            1 => '</p>
            </div>
            <a href="',
            2 => 23,
        ],
        34 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        36 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 25,
        ],
        38 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        39 => [
            0 => 'TextData',
            1 => '" class="ili-top-image"><img src="',
            2 => 25,
        ],
        40 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        42 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 25,
        ],
        44 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 25,
        ],
        46 => [
            0 => 'Colon',
            1 => ':',
            2 => 25,
        ],
        47 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 25,
        ],
        48 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        49 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 25,
        ],
        50 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        52 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 25,
        ],
        54 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 25,
        ],
        56 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        57 => [
            0 => 'TextData',
            1 => '"/></a>
          </div>

          <div class="ili-bottom clearfix">
            <p class="hiddenvariants" style="display: none">',
            2 => 25,
        ],
        58 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 29,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 29,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 29,
        ],
        63 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 29,
        ],
        65 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        66 => [
            0 => 'TextData',
            1 => '<span><input type="radio" name="id" value="',
            2 => 29,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        69 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        71 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        72 => [
            0 => 'TextData',
            1 => '" id="radio_',
            2 => 29,
        ],
        73 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        75 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        77 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        78 => [
            0 => 'TextData',
            1 => '" style="vertical-align: middle;" ',
            2 => 29,
        ],
        79 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 29,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 29,
        ],
        82 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 29,
        ],
        84 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        85 => [
            0 => 'TextData',
            1 => ' checked="checked" ',
            2 => 29,
        ],
        86 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 29,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        89 => [
            0 => 'TextData',
            1 => ' /><label for="radio_',
            2 => 29,
        ],
        90 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        92 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        94 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        95 => [
            0 => 'TextData',
            1 => '">',
            2 => 29,
        ],
        96 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        98 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 29,
        ],
        100 => [
            0 => 'Pipe',
            1 => '|',
            2 => 29,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 29,
        ],
        102 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        103 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 29,
        ],
        104 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 29,
        ],
        108 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        109 => [
            0 => 'TextData',
            1 => '</label></span>',
            2 => 29,
        ],
        110 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 29,
        ],
        112 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        113 => [
            0 => 'TextData',
            1 => '</p>
            <input type="submit" class="" value="Add to Basket" />
            <p>
              <a href="',
            2 => 29,
        ],
        114 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 32,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 32,
        ],
        118 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        119 => [
            0 => 'TextData',
            1 => '">View Details</a>

              <span>
                ',
            2 => 32,
        ],
        120 => [
            0 => 'BlockStart',
            1 => '',
            2 => 35,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 35,
        ],
        122 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 35,
        ],
        123 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        124 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 35,
        ],
        125 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 35,
        ],
        126 => [
            0 => 'TextData',
            1 => '
                  ',
            2 => 35,
        ],
        127 => [
            0 => 'BlockStart',
            1 => '',
            2 => 36,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 36,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        130 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 36,
        ],
        132 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 36,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        134 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 36,
        ],
        136 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 36,
        ],
        137 => [
            0 => 'TextData',
            1 => '
                    ',
            2 => 36,
        ],
        138 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 37,
        ],
        140 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 37,
        ],
        142 => [
            0 => 'Pipe',
            1 => '|',
            2 => 37,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 37,
        ],
        144 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        145 => [
            0 => 'TextData',
            1 => ' -
                    ',
            2 => 37,
        ],
        146 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 38,
        ],
        148 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        149 => [
            0 => 'TextData',
            1 => '
                ',
            2 => 38,
        ],
        150 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 39,
        ],
        152 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        153 => [
            0 => 'TextData',
            1 => '
                <strong>
                  ',
            2 => 39,
        ],
        154 => [
            0 => 'VariableStart',
            1 => '',
            2 => 41,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 41,
        ],
        156 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        157 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 41,
        ],
        158 => [
            0 => 'Pipe',
            1 => '|',
            2 => 41,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 41,
        ],
        160 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 41,
        ],
        161 => [
            0 => 'TextData',
            1 => '
                </strong>
              </span>
            </p>
          </div>
        </div>
        </form>
      </li>
      ',
            2 => 41,
        ],
        162 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 49,
        ],
        164 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        165 => [
            0 => 'TextData',
            1 => '

    </ul>
  </div>
  <!-- end page -->



',
            2 => 49,
        ],
    ],
    'performance/tests/tribble/article.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '
  <div id="page" class="innerpage clearfix">
    <div id="text-page">

          <div class="entry">
            <h1><span>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 6,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        6 => [
            0 => 'TextData',
            1 => '</span></h1>
            <div class="entry-post">
              <div class="meta">',
            2 => 6,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 8,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 8,
        ],
        11 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 8,
        ],
        13 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        14 => [
            0 => 'String',
            1 => '"%b %d"',
            2 => 8,
        ],
        15 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        16 => [
            0 => 'TextData',
            1 => '</div>
              ',
            2 => 8,
        ],
        17 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 9,
        ],
        19 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 9,
        ],
        21 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        22 => [
            0 => 'TextData',
            1 => '
            </div>

  <!-- Comments -->
',
            2 => 9,
        ],
        23 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 13,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 13,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 13,
        ],
        28 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        29 => [
            0 => 'TextData',
            1 => '
<div id="comments">
  <h2>Comments</h2>

  <!-- List all comments -->
  <ul id="comment-list">
  ',
            2 => 13,
        ],
        30 => [
            0 => 'BlockStart',
            1 => '',
            2 => 19,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 19,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 19,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 19,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 19,
        ],
        35 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'comments',
            2 => 19,
        ],
        37 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 19,
        ],
        38 => [
            0 => 'TextData',
            1 => '
    <li>
      <div class="comment">
        ',
            2 => 19,
        ],
        39 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 22,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 22,
        ],
        43 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        44 => [
            0 => 'TextData',
            1 => '
      </div>

      <div class="comment-details">
        Posted by <span class="comment-author">',
            2 => 22,
        ],
        45 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 26,
        ],
        47 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 26,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        50 => [
            0 => 'TextData',
            1 => '</span> on <span class="comment-date">',
            2 => 26,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 26,
        ],
        53 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 26,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 26,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 26,
        ],
        57 => [
            0 => 'Colon',
            1 => ':',
            2 => 26,
        ],
        58 => [
            0 => 'String',
            1 => '"%B %d, %Y"',
            2 => 26,
        ],
        59 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        60 => [
            0 => 'TextData',
            1 => '</span>
      </div>
    </li>
  ',
            2 => 26,
        ],
        61 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 29,
        ],
        63 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        64 => [
            0 => 'TextData',
            1 => '
  </ul>

  <!-- Comment Form -->
  <div id="comment-form">
  ',
            2 => 29,
        ],
        65 => [
            0 => 'BlockStart',
            1 => '',
            2 => 34,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 34,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 34,
        ],
        68 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 34,
        ],
        69 => [
            0 => 'TextData',
            1 => '
    <h2>Leave a comment</h2>

    <!-- Check if a comment has been submitted in the last request, and if yes display an appropriate message -->
    ',
            2 => 34,
        ],
        70 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 38,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 38,
        ],
        73 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'posted_successfully?',
            2 => 38,
        ],
        75 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        76 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 38,
        ],
        77 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 39,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 39,
        ],
        80 => [
            0 => 'Dot',
            1 => '.',
            2 => 39,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 39,
        ],
        82 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        83 => [
            0 => 'TextData',
            1 => '
        <div class="notice">
          Successfully posted your comment.<br />
          It will have to be approved by the blog owner first before showing up.
        </div>
      ',
            2 => 39,
        ],
        84 => [
            0 => 'BlockStart',
            1 => '',
            2 => 44,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 44,
        ],
        86 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 44,
        ],
        87 => [
            0 => 'TextData',
            1 => '
        <div class="notice">Successfully posted your comment.</div>
      ',
            2 => 44,
        ],
        88 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 46,
        ],
        90 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        91 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 46,
        ],
        92 => [
            0 => 'BlockStart',
            1 => '',
            2 => 47,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 47,
        ],
        94 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 47,
        ],
        95 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 47,
        ],
        96 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 49,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 49,
        ],
        99 => [
            0 => 'Dot',
            1 => '.',
            2 => 49,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 49,
        ],
        101 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        102 => [
            0 => 'TextData',
            1 => '
      <div class="notice error">Not all the fields have been filled out correctly!</div>
    ',
            2 => 49,
        ],
        103 => [
            0 => 'BlockStart',
            1 => '',
            2 => 51,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 51,
        ],
        105 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 51,
        ],
        106 => [
            0 => 'TextData',
            1 => '

    <dl>
      <dt class="',
            2 => 51,
        ],
        107 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 54,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 54,
        ],
        110 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 54,
        ],
        112 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 54,
        ],
        113 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 54,
        ],
        114 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        115 => [
            0 => 'TextData',
            1 => 'error',
            2 => 54,
        ],
        116 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 54,
        ],
        118 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        119 => [
            0 => 'TextData',
            1 => '"><label for="comment_author">Your name</label></dt>
      <dd><input type="text" id="comment_author" name="comment[author]" size="40" value="',
            2 => 54,
        ],
        120 => [
            0 => 'VariableStart',
            1 => '',
            2 => 55,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 55,
        ],
        122 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 55,
        ],
        124 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 55,
        ],
        125 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 55,
        ],
        126 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 55,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 55,
        ],
        129 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 55,
        ],
        131 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 55,
        ],
        132 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 55,
        ],
        133 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        134 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 55,
        ],
        135 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 55,
        ],
        137 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        138 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 55,
        ],
        139 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 57,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 57,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 57,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 57,
        ],
        144 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 57,
        ],
        145 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 57,
        ],
        146 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        147 => [
            0 => 'TextData',
            1 => 'error',
            2 => 57,
        ],
        148 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 57,
        ],
        150 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        151 => [
            0 => 'TextData',
            1 => '"><label for="comment_email">Your email</label></dt>
      <dd><input type="text" id="comment_email" name="comment[email]" size="40" value="',
            2 => 57,
        ],
        152 => [
            0 => 'VariableStart',
            1 => '',
            2 => 58,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 58,
        ],
        154 => [
            0 => 'Dot',
            1 => '.',
            2 => 58,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'email',
            2 => 58,
        ],
        156 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 58,
        ],
        157 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 58,
        ],
        158 => [
            0 => 'BlockStart',
            1 => '',
            2 => 58,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 58,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 58,
        ],
        161 => [
            0 => 'Dot',
            1 => '.',
            2 => 58,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 58,
        ],
        163 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 58,
        ],
        164 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 58,
        ],
        165 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 58,
        ],
        166 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 58,
        ],
        167 => [
            0 => 'BlockStart',
            1 => '',
            2 => 58,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 58,
        ],
        169 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 58,
        ],
        170 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 58,
        ],
        171 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        172 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 60,
        ],
        173 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 60,
        ],
        174 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 60,
        ],
        176 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 60,
        ],
        177 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 60,
        ],
        178 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        179 => [
            0 => 'TextData',
            1 => 'error',
            2 => 60,
        ],
        180 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 60,
        ],
        182 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        183 => [
            0 => 'TextData',
            1 => '"><label for="comment_body">Your comment</label></dt>
      <dd><textarea id="comment_body" name="comment[body]" cols="40" rows="5" class="',
            2 => 60,
        ],
        184 => [
            0 => 'BlockStart',
            1 => '',
            2 => 61,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 61,
        ],
        186 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 61,
        ],
        187 => [
            0 => 'Dot',
            1 => '.',
            2 => 61,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 61,
        ],
        189 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 61,
        ],
        190 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 61,
        ],
        191 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 61,
        ],
        192 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 61,
        ],
        193 => [
            0 => 'BlockStart',
            1 => '',
            2 => 61,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 61,
        ],
        195 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 61,
        ],
        196 => [
            0 => 'TextData',
            1 => '">',
            2 => 61,
        ],
        197 => [
            0 => 'VariableStart',
            1 => '',
            2 => 61,
        ],
        198 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 61,
        ],
        199 => [
            0 => 'Dot',
            1 => '.',
            2 => 61,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'body',
            2 => 61,
        ],
        201 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 61,
        ],
        202 => [
            0 => 'TextData',
            1 => '</textarea></dd>
    </dl>

    ',
            2 => 61,
        ],
        203 => [
            0 => 'BlockStart',
            1 => '',
            2 => 64,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 64,
        ],
        205 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 64,
        ],
        206 => [
            0 => 'Dot',
            1 => '.',
            2 => 64,
        ],
        207 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 64,
        ],
        208 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 64,
        ],
        209 => [
            0 => 'TextData',
            1 => '
      <p class="hint">comments have to be approved before showing up</p>
    ',
            2 => 64,
        ],
        210 => [
            0 => 'BlockStart',
            1 => '',
            2 => 66,
        ],
        211 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 66,
        ],
        212 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 66,
        ],
        213 => [
            0 => 'TextData',
            1 => '

    <input type="submit" value="Post comment" id="comment-submit" />
  ',
            2 => 66,
        ],
        214 => [
            0 => 'BlockStart',
            1 => '',
            2 => 69,
        ],
        215 => [
            0 => 'Identifier',
            1 => 'endform',
            2 => 69,
        ],
        216 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 69,
        ],
        217 => [
            0 => 'TextData',
            1 => '
  </div>
  <!-- END Comment Form -->

</div>
',
            2 => 69,
        ],
        218 => [
            0 => 'BlockStart',
            1 => '',
            2 => 74,
        ],
        219 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 74,
        ],
        220 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 74,
        ],
        221 => [
            0 => 'TextData',
            1 => '
<!-- END Comments -->


          </div>
    </div>

    <div id="three-reasons" class="clearfix">
      <h3>Why Shop With Us?</h3>
      <ul>
        <li class="two-a">
          <h4>24 Hours</h4>
          <p>We\'re always here to help.</p>
        </li>
        <li class="two-c">
          <h4>No Spam</h4>
          <p>We\'ll never share your info.</p>
        </li>
        <li class="two-d">
          <h4>Secure Servers</h4>
          <p>Checkout is 256bit encrypted.</p>
        </li>
      </ul>
    </div>
  </div>
',
            2 => 74,
        ],
    ],
    'performance/tests/tribble/blog.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="page" class="innerpage clearfix">
    <div id="text-page">
      <h1>Post from our blog...</h1>
      ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 4,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 4,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 4,
        ],
        6 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 4,
        ],
        7 => [
            0 => 'Number',
            1 => '20',
            2 => 4,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        9 => [
            0 => 'TextData',
            1 => '
          ',
            2 => 4,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 5,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 5,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 5,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 5,
        ],
        15 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 5,
        ],
        17 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        18 => [
            0 => 'TextData',
            1 => '

          <div class="entry">
            <h1><span><a href="',
            2 => 5,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 8,
        ],
        21 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 8,
        ],
        23 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        24 => [
            0 => 'TextData',
            1 => '">',
            2 => 8,
        ],
        25 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 8,
        ],
        27 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 8,
        ],
        29 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        30 => [
            0 => 'TextData',
            1 => '</a></span></h1>
            <div class="entry-post">
              <div class="meta">',
            2 => 8,
        ],
        31 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 10,
        ],
        33 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 10,
        ],
        35 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 10,
        ],
        37 => [
            0 => 'Colon',
            1 => ':',
            2 => 10,
        ],
        38 => [
            0 => 'String',
            1 => '"%b %d"',
            2 => 10,
        ],
        39 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        40 => [
            0 => 'TextData',
            1 => '</div>
              ',
            2 => 10,
        ],
        41 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 11,
        ],
        43 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 11,
        ],
        45 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        46 => [
            0 => 'TextData',
            1 => '
            </div>
          </div>

        ',
            2 => 11,
        ],
        47 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 15,
        ],
        49 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        50 => [
            0 => 'TextData',
            1 => '

        <div class="paginate clearfix">
            ',
            2 => 15,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 18,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 18,
        ],
        55 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        56 => [
            0 => 'TextData',
            1 => '
        </div>

      ',
            2 => 18,
        ],
        57 => [
            0 => 'BlockStart',
            1 => '',
            2 => 21,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 21,
        ],
        59 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 21,
        ],
        60 => [
            0 => 'TextData',
            1 => '
    </div>

    <div id="three-reasons" class="clearfix">
      <h3>Why Shop With Us?</h3>
      <ul>
        <li class="two-a">
          <h4>24 Hours</h4>
          <p>We\'re always here to help.</p>
        </li>
        <li class="two-c">
          <h4>No Spam</h4>
          <p>We\'ll never share your info.</p>
        </li>
        <li class="two-d">
          <h4>Secure Servers</h4>
          <p>Checkout is 256bit encrypted.</p>
        </li>
      </ul>
    </div>
  </div>
',
            2 => 21,
        ],
    ],
    'performance/tests/tribble/cart.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<script type="text/javascript">
  function remove_item(id) {
      document.getElementById(\'updates_\'+id).value = 0;
      document.getElementById(\'cart\').submit();
  }
</script>

  <div id="page" class="innerpage clearfix">.
    ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 9,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 9,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 9,
        ],
        6 => [
            0 => 'Comparison',
            1 => '==',
            2 => 9,
        ],
        7 => [
            0 => 'Number',
            1 => '0',
            2 => 9,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        9 => [
            0 => 'TextData',
            1 => '
         <h1>Your cart is currently empty.</h1>
      ',
            2 => 9,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 11,
        ],
        12 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        13 => [
            0 => 'TextData',
            1 => '

    <h1>Your Cart <span>(',
            2 => 11,
        ],
        14 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 13,
        ],
        16 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 13,
        ],
        18 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        19 => [
            0 => 'TextData',
            1 => ' ',
            2 => 13,
        ],
        20 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 13,
        ],
        22 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 13,
        ],
        24 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 13,
        ],
        26 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        27 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 13,
        ],
        28 => [
            0 => 'Comma',
            1 => ',',
            2 => 13,
        ],
        29 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 13,
        ],
        30 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        31 => [
            0 => 'TextData',
            1 => ', ',
            2 => 13,
        ],
        32 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 13,
        ],
        34 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 13,
        ],
        36 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 13,
        ],
        38 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        39 => [
            0 => 'TextData',
            1 => ' total)</span></h1>

    <form action="/cart" method="post" id="cart-form">

    <div id="cart-wrap">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <th scope="col" class="td-image"><label>Image</label></th>
          <th scope="col" class="td-title"><label>Product Title</label></th>
          <th scope="col" class="td-count"><label>Count</label></th>
          <th scope="col" class="td-price"><label>Cost</label></th>
          <th scope="col" class="td-delete"><label>Remove</label></th>
        </tr>

        ',
            2 => 13,
        ],
        40 => [
            0 => 'BlockStart',
            1 => '',
            2 => 27,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 27,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 27,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 27,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 27,
        ],
        45 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'items',
            2 => 27,
        ],
        47 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 27,
        ],
        48 => [
            0 => 'TextData',
            1 => '
        <tr class="',
            2 => 27,
        ],
        49 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'cycle',
            2 => 28,
        ],
        51 => [
            0 => 'String',
            1 => '\'reg\'',
            2 => 28,
        ],
        52 => [
            0 => 'Comma',
            1 => ',',
            2 => 28,
        ],
        53 => [
            0 => 'String',
            1 => '\'alt\'',
            2 => 28,
        ],
        54 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        55 => [
            0 => 'TextData',
            1 => '">
          <td colspan="5">
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td class="td-image"><a href="',
            2 => 28,
        ],
        56 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 32,
        ],
        58 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 32,
        ],
        60 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 32,
        ],
        62 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        63 => [
            0 => 'TextData',
            1 => '">',
            2 => 32,
        ],
        64 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 32,
        ],
        66 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 32,
        ],
        68 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 32,
        ],
        70 => [
            0 => 'Pipe',
            1 => '|',
            2 => 32,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 32,
        ],
        72 => [
            0 => 'Colon',
            1 => ':',
            2 => 32,
        ],
        73 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 32,
        ],
        74 => [
            0 => 'Pipe',
            1 => '|',
            2 => 32,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'img_tag',
            2 => 32,
        ],
        76 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        77 => [
            0 => 'TextData',
            1 => '</a></td>
                <td class="td-title"><p>',
            2 => 32,
        ],
        78 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 33,
        ],
        80 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 33,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        83 => [
            0 => 'TextData',
            1 => '</p></td>
                <td class="td-count"><label>Count:</label> <input type="text" class="quantity item-count" name="updates[',
            2 => 33,
        ],
        84 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 34,
        ],
        88 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 34,
        ],
        90 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        91 => [
            0 => 'TextData',
            1 => ']" id="updates_',
            2 => 34,
        ],
        92 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        94 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 34,
        ],
        96 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 34,
        ],
        98 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        99 => [
            0 => 'TextData',
            1 => '" value="',
            2 => 34,
        ],
        100 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        102 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'quantity',
            2 => 34,
        ],
        104 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        105 => [
            0 => 'TextData',
            1 => '" onfocus="this.select();"/></td>
                <td class="td-price">',
            2 => 34,
        ],
        106 => [
            0 => 'VariableStart',
            1 => '',
            2 => 35,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 35,
        ],
        108 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'line_price',
            2 => 35,
        ],
        110 => [
            0 => 'Pipe',
            1 => '|',
            2 => 35,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 35,
        ],
        112 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 35,
        ],
        113 => [
            0 => 'TextData',
            1 => '</td>
                <td class="td-delete"><a href="#" onclick="remove_item(',
            2 => 35,
        ],
        114 => [
            0 => 'VariableStart',
            1 => '',
            2 => 36,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 36,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 36,
        ],
        118 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 36,
        ],
        120 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 36,
        ],
        121 => [
            0 => 'TextData',
            1 => '); return false;">Remove</a></td>
              </tr>
            </table>
          </td>
        </tr>
        ',
            2 => 36,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 41,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 41,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 41,
        ],
        125 => [
            0 => 'TextData',
            1 => '
      </table>

      <div id="finish-up">

        <div class="latest-news-box">
          ',
            2 => 41,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 47,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'shopping-cart',
            2 => 47,
        ],
        130 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 47,
        ],
        132 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        133 => [
            0 => 'TextData',
            1 => '
        </div>

        <p class="order-total">
          <span><strong>Order Total:</strong> ',
            2 => 47,
        ],
        134 => [
            0 => 'VariableStart',
            1 => '',
            2 => 51,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 51,
        ],
        136 => [
            0 => 'Dot',
            1 => '.',
            2 => 51,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 51,
        ],
        138 => [
            0 => 'Pipe',
            1 => '|',
            2 => 51,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 51,
        ],
        140 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 51,
        ],
        141 => [
            0 => 'TextData',
            1 => '</span>
        </p>

        <p class="update-cart"><input type="submit" value="Refresh Cart" name="update" /></p>

          <p class="go-checkout"><input type="submit" value="Proceed to Checkout" name="checkout"  /></p>

        ',
            2 => 51,
        ],
        142 => [
            0 => 'BlockStart',
            1 => '',
            2 => 58,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 58,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'additional_checkout_buttons',
            2 => 58,
        ],
        145 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 58,
        ],
        146 => [
            0 => 'TextData',
            1 => '
        <div class="additional-checkout-buttons">
          <p>- or -</p>
          ',
            2 => 58,
        ],
        147 => [
            0 => 'VariableStart',
            1 => '',
            2 => 61,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'content_for_additional_checkout_buttons',
            2 => 61,
        ],
        149 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 61,
        ],
        150 => [
            0 => 'TextData',
            1 => '
        </div>
        ',
            2 => 61,
        ],
        151 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        152 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 63,
        ],
        153 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        154 => [
            0 => 'TextData',
            1 => '

      </div>

    </div>

    </form>

    ',
            2 => 63,
        ],
        155 => [
            0 => 'BlockStart',
            1 => '',
            2 => 71,
        ],
        156 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 71,
        ],
        157 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 71,
        ],
        158 => [
            0 => 'TextData',
            1 => '



    <h1 class="other-products"><span>Other Products You Might Enjoy</span></h1>
    <ul class="item-list clearfix">

      ',
            2 => 71,
        ],
        159 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 78,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 78,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 78,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 78,
        ],
        164 => [
            0 => 'Dot',
            1 => '.',
            2 => 78,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 78,
        ],
        166 => [
            0 => 'Dot',
            1 => '.',
            2 => 78,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 78,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 78,
        ],
        169 => [
            0 => 'Colon',
            1 => ':',
            2 => 78,
        ],
        170 => [
            0 => 'Number',
            1 => '2',
            2 => 78,
        ],
        171 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        172 => [
            0 => 'TextData',
            1 => '
      <li>
        <form action="/cart/add" method="post">
        <div class="item-list-item">
          <div class="ili-top clearfix">
            <div class="ili-top-content">
              <h2><a href="',
            2 => 78,
        ],
        173 => [
            0 => 'VariableStart',
            1 => '',
            2 => 84,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 84,
        ],
        175 => [
            0 => 'Dot',
            1 => '.',
            2 => 84,
        ],
        176 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 84,
        ],
        177 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 84,
        ],
        178 => [
            0 => 'TextData',
            1 => '">',
            2 => 84,
        ],
        179 => [
            0 => 'VariableStart',
            1 => '',
            2 => 84,
        ],
        180 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 84,
        ],
        181 => [
            0 => 'Dot',
            1 => '.',
            2 => 84,
        ],
        182 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 84,
        ],
        183 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 84,
        ],
        184 => [
            0 => 'TextData',
            1 => '</a></h2>
              <p>',
            2 => 84,
        ],
        185 => [
            0 => 'VariableStart',
            1 => '',
            2 => 85,
        ],
        186 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 85,
        ],
        187 => [
            0 => 'Dot',
            1 => '.',
            2 => 85,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 85,
        ],
        189 => [
            0 => 'Pipe',
            1 => '|',
            2 => 85,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 85,
        ],
        191 => [
            0 => 'Colon',
            1 => ':',
            2 => 85,
        ],
        192 => [
            0 => 'Number',
            1 => '15',
            2 => 85,
        ],
        193 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 85,
        ],
        194 => [
            0 => 'TextData',
            1 => '</p>
            </div>
            <a href="',
            2 => 85,
        ],
        195 => [
            0 => 'VariableStart',
            1 => '',
            2 => 87,
        ],
        196 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 87,
        ],
        197 => [
            0 => 'Dot',
            1 => '.',
            2 => 87,
        ],
        198 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 87,
        ],
        199 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 87,
        ],
        200 => [
            0 => 'TextData',
            1 => '" class="ili-top-image"><img src="',
            2 => 87,
        ],
        201 => [
            0 => 'VariableStart',
            1 => '',
            2 => 87,
        ],
        202 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 87,
        ],
        203 => [
            0 => 'Dot',
            1 => '.',
            2 => 87,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 87,
        ],
        205 => [
            0 => 'Pipe',
            1 => '|',
            2 => 87,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 87,
        ],
        207 => [
            0 => 'Colon',
            1 => ':',
            2 => 87,
        ],
        208 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 87,
        ],
        209 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 87,
        ],
        210 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 87,
        ],
        211 => [
            0 => 'VariableStart',
            1 => '',
            2 => 87,
        ],
        212 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 87,
        ],
        213 => [
            0 => 'Dot',
            1 => '.',
            2 => 87,
        ],
        214 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 87,
        ],
        215 => [
            0 => 'Pipe',
            1 => '|',
            2 => 87,
        ],
        216 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 87,
        ],
        217 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 87,
        ],
        218 => [
            0 => 'TextData',
            1 => '"/></a>
          </div>

          <div class="ili-bottom clearfix">
            <p class="hiddenvariants" style="display: none">',
            2 => 87,
        ],
        219 => [
            0 => 'BlockStart',
            1 => '',
            2 => 91,
        ],
        220 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 91,
        ],
        221 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        222 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 91,
        ],
        223 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 91,
        ],
        224 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        225 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 91,
        ],
        226 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 91,
        ],
        227 => [
            0 => 'TextData',
            1 => '<span><input type="radio" name="id" value="',
            2 => 91,
        ],
        228 => [
            0 => 'VariableStart',
            1 => '',
            2 => 91,
        ],
        229 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        230 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        231 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 91,
        ],
        232 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 91,
        ],
        233 => [
            0 => 'TextData',
            1 => '" id="radio_',
            2 => 91,
        ],
        234 => [
            0 => 'VariableStart',
            1 => '',
            2 => 91,
        ],
        235 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        236 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        237 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 91,
        ],
        238 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 91,
        ],
        239 => [
            0 => 'TextData',
            1 => '" style="vertical-align: middle;" ',
            2 => 91,
        ],
        240 => [
            0 => 'BlockStart',
            1 => '',
            2 => 91,
        ],
        241 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 91,
        ],
        242 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 91,
        ],
        243 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        244 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 91,
        ],
        245 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 91,
        ],
        246 => [
            0 => 'TextData',
            1 => ' checked="checked" ',
            2 => 91,
        ],
        247 => [
            0 => 'BlockStart',
            1 => '',
            2 => 91,
        ],
        248 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 91,
        ],
        249 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 91,
        ],
        250 => [
            0 => 'TextData',
            1 => ' /><label for="radio_',
            2 => 91,
        ],
        251 => [
            0 => 'VariableStart',
            1 => '',
            2 => 91,
        ],
        252 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        253 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        254 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 91,
        ],
        255 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 91,
        ],
        256 => [
            0 => 'TextData',
            1 => '">',
            2 => 91,
        ],
        257 => [
            0 => 'VariableStart',
            1 => '',
            2 => 91,
        ],
        258 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        259 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        260 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 91,
        ],
        261 => [
            0 => 'Pipe',
            1 => '|',
            2 => 91,
        ],
        262 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 91,
        ],
        263 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 91,
        ],
        264 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 91,
        ],
        265 => [
            0 => 'VariableStart',
            1 => '',
            2 => 91,
        ],
        266 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 91,
        ],
        267 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        268 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 91,
        ],
        269 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 91,
        ],
        270 => [
            0 => 'TextData',
            1 => '</label></span>',
            2 => 91,
        ],
        271 => [
            0 => 'BlockStart',
            1 => '',
            2 => 91,
        ],
        272 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 91,
        ],
        273 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 91,
        ],
        274 => [
            0 => 'TextData',
            1 => '</p>
            <input type="submit" class="" value="Add to Basket" />
            <p>
              <a href="',
            2 => 91,
        ],
        275 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        276 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        277 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        278 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 94,
        ],
        279 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        280 => [
            0 => 'TextData',
            1 => '">View Details</a>

              <span>
                ',
            2 => 94,
        ],
        281 => [
            0 => 'BlockStart',
            1 => '',
            2 => 97,
        ],
        282 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 97,
        ],
        283 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 97,
        ],
        284 => [
            0 => 'Dot',
            1 => '.',
            2 => 97,
        ],
        285 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 97,
        ],
        286 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 97,
        ],
        287 => [
            0 => 'TextData',
            1 => '
                  ',
            2 => 97,
        ],
        288 => [
            0 => 'BlockStart',
            1 => '',
            2 => 98,
        ],
        289 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 98,
        ],
        290 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 98,
        ],
        291 => [
            0 => 'Dot',
            1 => '.',
            2 => 98,
        ],
        292 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 98,
        ],
        293 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 98,
        ],
        294 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 98,
        ],
        295 => [
            0 => 'Dot',
            1 => '.',
            2 => 98,
        ],
        296 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 98,
        ],
        297 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 98,
        ],
        298 => [
            0 => 'TextData',
            1 => '
                    ',
            2 => 98,
        ],
        299 => [
            0 => 'VariableStart',
            1 => '',
            2 => 99,
        ],
        300 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 99,
        ],
        301 => [
            0 => 'Dot',
            1 => '.',
            2 => 99,
        ],
        302 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 99,
        ],
        303 => [
            0 => 'Pipe',
            1 => '|',
            2 => 99,
        ],
        304 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 99,
        ],
        305 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 99,
        ],
        306 => [
            0 => 'TextData',
            1 => ' -
                    ',
            2 => 99,
        ],
        307 => [
            0 => 'BlockStart',
            1 => '',
            2 => 100,
        ],
        308 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 100,
        ],
        309 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 100,
        ],
        310 => [
            0 => 'TextData',
            1 => '
                ',
            2 => 100,
        ],
        311 => [
            0 => 'BlockStart',
            1 => '',
            2 => 101,
        ],
        312 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 101,
        ],
        313 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 101,
        ],
        314 => [
            0 => 'TextData',
            1 => '
                <strong>
                  ',
            2 => 101,
        ],
        315 => [
            0 => 'VariableStart',
            1 => '',
            2 => 103,
        ],
        316 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 103,
        ],
        317 => [
            0 => 'Dot',
            1 => '.',
            2 => 103,
        ],
        318 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 103,
        ],
        319 => [
            0 => 'Pipe',
            1 => '|',
            2 => 103,
        ],
        320 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 103,
        ],
        321 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 103,
        ],
        322 => [
            0 => 'TextData',
            1 => '
                </strong>
              </span>
            </p>
          </div>
        </div>
        </form>
      </li>
      ',
            2 => 103,
        ],
        323 => [
            0 => 'BlockStart',
            1 => '',
            2 => 111,
        ],
        324 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 111,
        ],
        325 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 111,
        ],
        326 => [
            0 => 'TextData',
            1 => '

    </ul>

    <div id="three-reasons" class="clearfix">
      <h3>Why Shop With Us?</h3>
      <ul>
        <li class="two-a">
          <h4>24 Hours</h4>
          <p>We\'re always here to help.</p>
        </li>
        <li class="two-c">
          <h4>No Spam</h4>
          <p>We\'ll never share your info.</p>
        </li>
        <li class="two-d">
          <h4>Secure Servers</h4>
          <p>Checkout is 256bit encrypted.</p>
        </li>
      </ul>
    </div>

  </div>
  <!-- end page -->
',
            2 => 111,
        ],
    ],
    'performance/tests/tribble/collection.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="page" class="innerpage clearfix">
    <h1>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h1>
    ',
            2 => 2,
        ],
        7 => [
            0 => 'BlockStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 3,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 3,
        ],
        10 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 3,
        ],
        12 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'size',
            2 => 3,
        ],
        14 => [
            0 => 'Comparison',
            1 => '>',
            2 => 3,
        ],
        15 => [
            0 => 'Number',
            1 => '0',
            2 => 3,
        ],
        16 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 3,
        ],
        17 => [
            0 => 'TextData',
            1 => '
      <div class="latest-news">',
            2 => 3,
        ],
        18 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 4,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 4,
        ],
        22 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        23 => [
            0 => 'TextData',
            1 => '</div>
    ',
            2 => 4,
        ],
        24 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 5,
        ],
        26 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        27 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 5,
        ],
        28 => [
            0 => 'BlockStart',
            1 => '',
            2 => 7,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 7,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 7,
        ],
        31 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 7,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 7,
        ],
        34 => [
            0 => 'Number',
            1 => '8',
            2 => 7,
        ],
        35 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 7,
        ],
        36 => [
            0 => 'TextData',
            1 => '

    <ul class="item-list clearfix">
    ',
            2 => 7,
        ],
        37 => [
            0 => 'BlockStart',
            1 => '',
            2 => 10,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 10,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 10,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 10,
        ],
        42 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 10,
        ],
        44 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 10,
        ],
        45 => [
            0 => 'TextData',
            1 => '
      <li>
        <form action="/cart/add" method="post">
        <div class="item-list-item">
          <div class="ili-top clearfix">
            <div class="ili-top-content">
              <h2><a href="',
            2 => 10,
        ],
        46 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        48 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 16,
        ],
        50 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        51 => [
            0 => 'TextData',
            1 => '">',
            2 => 16,
        ],
        52 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        54 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 16,
        ],
        56 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        57 => [
            0 => 'TextData',
            1 => '</a></h2>
              <p>',
            2 => 16,
        ],
        58 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 17,
        ],
        60 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 17,
        ],
        62 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 17,
        ],
        64 => [
            0 => 'Colon',
            1 => ':',
            2 => 17,
        ],
        65 => [
            0 => 'Number',
            1 => '15',
            2 => 17,
        ],
        66 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        67 => [
            0 => 'TextData',
            1 => '</p>
            </div>
            <a href="',
            2 => 17,
        ],
        68 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        70 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 19,
        ],
        72 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        73 => [
            0 => 'TextData',
            1 => '" class="ili-top-image"><img src="',
            2 => 19,
        ],
        74 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        76 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 19,
        ],
        78 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 19,
        ],
        80 => [
            0 => 'Colon',
            1 => ':',
            2 => 19,
        ],
        81 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 19,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        83 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 19,
        ],
        84 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 19,
        ],
        88 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 19,
        ],
        90 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        91 => [
            0 => 'TextData',
            1 => '"/></a>
          </div>

          <div class="ili-bottom clearfix">
            <p class="hiddenvariants" style="display: none">',
            2 => 19,
        ],
        92 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 23,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 23,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        97 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 23,
        ],
        99 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        100 => [
            0 => 'TextData',
            1 => '<span><input type="radio" name="id" value="',
            2 => 23,
        ],
        101 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        103 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 23,
        ],
        105 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        106 => [
            0 => 'TextData',
            1 => '" id="radio_',
            2 => 23,
        ],
        107 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        109 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 23,
        ],
        111 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        112 => [
            0 => 'TextData',
            1 => '" style="vertical-align: middle;" ',
            2 => 23,
        ],
        113 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 23,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 23,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 23,
        ],
        118 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        119 => [
            0 => 'TextData',
            1 => ' checked="checked" ',
            2 => 23,
        ],
        120 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 23,
        ],
        122 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        123 => [
            0 => 'TextData',
            1 => ' /><label for="radio_',
            2 => 23,
        ],
        124 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        126 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 23,
        ],
        128 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        129 => [
            0 => 'TextData',
            1 => '">',
            2 => 23,
        ],
        130 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        132 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 23,
        ],
        134 => [
            0 => 'Pipe',
            1 => '|',
            2 => 23,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 23,
        ],
        136 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        137 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 23,
        ],
        138 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 23,
        ],
        140 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 23,
        ],
        142 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        143 => [
            0 => 'TextData',
            1 => '</label></span>',
            2 => 23,
        ],
        144 => [
            0 => 'BlockStart',
            1 => '',
            2 => 23,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 23,
        ],
        146 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 23,
        ],
        147 => [
            0 => 'TextData',
            1 => '</p>
            <input type="submit" class="" value="Add to Basket" />
            <p>
              <a href="',
            2 => 23,
        ],
        148 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 26,
        ],
        150 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 26,
        ],
        152 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        153 => [
            0 => 'TextData',
            1 => '">View Details</a>

              <span>
                ',
            2 => 26,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 29,
        ],
        156 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 29,
        ],
        157 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        158 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 29,
        ],
        159 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        160 => [
            0 => 'TextData',
            1 => '
                  ',
            2 => 29,
        ],
        161 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 30,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 30,
        ],
        164 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 30,
        ],
        166 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 30,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 30,
        ],
        168 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 30,
        ],
        170 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        171 => [
            0 => 'TextData',
            1 => '
                    ',
            2 => 30,
        ],
        172 => [
            0 => 'VariableStart',
            1 => '',
            2 => 31,
        ],
        173 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 31,
        ],
        174 => [
            0 => 'Dot',
            1 => '.',
            2 => 31,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 31,
        ],
        176 => [
            0 => 'Pipe',
            1 => '|',
            2 => 31,
        ],
        177 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 31,
        ],
        178 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 31,
        ],
        179 => [
            0 => 'TextData',
            1 => ' -
                    ',
            2 => 31,
        ],
        180 => [
            0 => 'BlockStart',
            1 => '',
            2 => 32,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 32,
        ],
        182 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 32,
        ],
        183 => [
            0 => 'TextData',
            1 => '
                ',
            2 => 32,
        ],
        184 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 33,
        ],
        186 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        187 => [
            0 => 'TextData',
            1 => '
                <strong>
                  ',
            2 => 33,
        ],
        188 => [
            0 => 'VariableStart',
            1 => '',
            2 => 35,
        ],
        189 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 35,
        ],
        190 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 35,
        ],
        192 => [
            0 => 'Pipe',
            1 => '|',
            2 => 35,
        ],
        193 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 35,
        ],
        194 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 35,
        ],
        195 => [
            0 => 'TextData',
            1 => '
                </strong>
              </span>
            </p>
          </div>
        </div>
        </form>
      </li>
    ',
            2 => 35,
        ],
        196 => [
            0 => 'BlockStart',
            1 => '',
            2 => 43,
        ],
        197 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 43,
        ],
        198 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 43,
        ],
        199 => [
            0 => 'TextData',
            1 => '
    </ul>

    <div class="paginate clearfix">
      ',
            2 => 43,
        ],
        200 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        201 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 47,
        ],
        202 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        203 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 47,
        ],
        204 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        205 => [
            0 => 'TextData',
            1 => '
    </div>


    <div id="three-reasons" class="clearfix">
      <h3>Why Shop With Us?</h3>
      <ul>
        <li class="two-a">
          <h4>24 Hours</h4>
          <p>We\'re always here to help.</p>
        </li>
        <li class="two-c">
          <h4>No Spam</h4>
          <p>We\'ll never share your info.</p>
        </li>
        <li class="two-d">
          <h4>Secure Servers</h4>
          <p>Checkout is 256bit encrypted.</p>
        </li>
      </ul>
    </div>
  </div>

',
            2 => 47,
        ],
        206 => [
            0 => 'BlockStart',
            1 => '',
            2 => 70,
        ],
        207 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 70,
        ],
        208 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 70,
        ],
        209 => [
            0 => 'TextData',
            1 => '
',
            2 => 70,
        ],
    ],
    'performance/tests/tribble/index.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="gwrap">
    <div id="gbox">
      <h1>Three Great Reasons You Should Shop With Us...</h1>
      <ul>
        <li class="gbox1">
          <h2>Free Shipping</h2>
          <p>On all orders over $25</p>
        </li>
        <li class="gbox2">
          <h2>Top Quality</h2>
          <p>Hand made in our shop</p>
        </li>
        <li class="gbox3">
          <h2>100% Guarantee</h2>
          <p>Any time, any reason</p>
        </li>
      </ul>
    </div>
  </div>

  <div id="page" class="clearfix">

    <div class="latest-news">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 23,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'alert',
            2 => 23,
        ],
        5 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        6 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 23,
        ],
        7 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        8 => [
            0 => 'TextData',
            1 => '</div>

    <ul class="item-list clearfix">

      ',
            2 => 23,
        ],
        9 => [
            0 => 'BlockStart',
            1 => '',
            2 => 27,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 27,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 27,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 27,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 27,
        ],
        14 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 27,
        ],
        16 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 27,
        ],
        18 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 27,
        ],
        19 => [
            0 => 'TextData',
            1 => '
      <li>
        <form action="/cart/add" method="post">
        <div class="item-list-item">
          <div class="ili-top clearfix">
            <div class="ili-top-content">
              <h2><a href="',
            2 => 27,
        ],
        20 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 33,
        ],
        22 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 33,
        ],
        24 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        25 => [
            0 => 'TextData',
            1 => '">',
            2 => 33,
        ],
        26 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 33,
        ],
        28 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 33,
        ],
        30 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        31 => [
            0 => 'TextData',
            1 => '</a></h2>
              ',
            2 => 33,
        ],
        32 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        34 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 34,
        ],
        36 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 34,
        ],
        38 => [
            0 => 'Colon',
            1 => ':',
            2 => 34,
        ],
        39 => [
            0 => 'Number',
            1 => '15',
            2 => 34,
        ],
        40 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        41 => [
            0 => 'TextData',
            1 => '</p> <!-- extra cloding <p> tag for truncation -->
            </div>
            <a href="',
            2 => 34,
        ],
        42 => [
            0 => 'VariableStart',
            1 => '',
            2 => 36,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        44 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 36,
        ],
        46 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 36,
        ],
        47 => [
            0 => 'TextData',
            1 => '" class="ili-top-image"><img src="',
            2 => 36,
        ],
        48 => [
            0 => 'VariableStart',
            1 => '',
            2 => 36,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        50 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 36,
        ],
        52 => [
            0 => 'Pipe',
            1 => '|',
            2 => 36,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 36,
        ],
        54 => [
            0 => 'Colon',
            1 => ':',
            2 => 36,
        ],
        55 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 36,
        ],
        56 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 36,
        ],
        57 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 36,
        ],
        58 => [
            0 => 'VariableStart',
            1 => '',
            2 => 36,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        60 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 36,
        ],
        62 => [
            0 => 'Pipe',
            1 => '|',
            2 => 36,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 36,
        ],
        64 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 36,
        ],
        65 => [
            0 => 'TextData',
            1 => '"/></a>
          </div>

          <div class="ili-bottom clearfix">
            <p class="hiddenvariants" style="display: none">',
            2 => 36,
        ],
        66 => [
            0 => 'BlockStart',
            1 => '',
            2 => 40,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 40,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 40,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 40,
        ],
        71 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 40,
        ],
        73 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 40,
        ],
        74 => [
            0 => 'TextData',
            1 => '<span><input type="radio" name="id" value="',
            2 => 40,
        ],
        75 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        77 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 40,
        ],
        79 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        80 => [
            0 => 'TextData',
            1 => '" id="radio_',
            2 => 40,
        ],
        81 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        83 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 40,
        ],
        85 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        86 => [
            0 => 'TextData',
            1 => '" style="vertical-align: middle;" ',
            2 => 40,
        ],
        87 => [
            0 => 'BlockStart',
            1 => '',
            2 => 40,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 40,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 40,
        ],
        90 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 40,
        ],
        92 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 40,
        ],
        93 => [
            0 => 'TextData',
            1 => ' checked="checked" ',
            2 => 40,
        ],
        94 => [
            0 => 'BlockStart',
            1 => '',
            2 => 40,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 40,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 40,
        ],
        97 => [
            0 => 'TextData',
            1 => ' /><label for="radio_',
            2 => 40,
        ],
        98 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        100 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 40,
        ],
        102 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        103 => [
            0 => 'TextData',
            1 => '">',
            2 => 40,
        ],
        104 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 40,
        ],
        108 => [
            0 => 'Pipe',
            1 => '|',
            2 => 40,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 40,
        ],
        110 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        111 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 40,
        ],
        112 => [
            0 => 'VariableStart',
            1 => '',
            2 => 40,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 40,
        ],
        114 => [
            0 => 'Dot',
            1 => '.',
            2 => 40,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 40,
        ],
        116 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 40,
        ],
        117 => [
            0 => 'TextData',
            1 => '</label></span>',
            2 => 40,
        ],
        118 => [
            0 => 'BlockStart',
            1 => '',
            2 => 40,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 40,
        ],
        120 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 40,
        ],
        121 => [
            0 => 'TextData',
            1 => '</p>
            <input type="submit" class="" value="Add to Basket" />
            <p>
              <a href="',
            2 => 40,
        ],
        122 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 43,
        ],
        124 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 43,
        ],
        126 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        127 => [
            0 => 'TextData',
            1 => '">View Details</a>

              <span>
                ',
            2 => 43,
        ],
        128 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 46,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 46,
        ],
        131 => [
            0 => 'Dot',
            1 => '.',
            2 => 46,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 46,
        ],
        133 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        134 => [
            0 => 'TextData',
            1 => '
                  ',
            2 => 46,
        ],
        135 => [
            0 => 'BlockStart',
            1 => '',
            2 => 47,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 47,
        ],
        137 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 47,
        ],
        138 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 47,
        ],
        140 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 47,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 47,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 47,
        ],
        144 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 47,
        ],
        145 => [
            0 => 'TextData',
            1 => '
                    ',
            2 => 47,
        ],
        146 => [
            0 => 'VariableStart',
            1 => '',
            2 => 48,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 48,
        ],
        148 => [
            0 => 'Dot',
            1 => '.',
            2 => 48,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 48,
        ],
        150 => [
            0 => 'Pipe',
            1 => '|',
            2 => 48,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 48,
        ],
        152 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 48,
        ],
        153 => [
            0 => 'TextData',
            1 => ' -
                    ',
            2 => 48,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 49,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        157 => [
            0 => 'TextData',
            1 => '
                ',
            2 => 49,
        ],
        158 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 50,
        ],
        160 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        161 => [
            0 => 'TextData',
            1 => '
                <strong>
                  ',
            2 => 50,
        ],
        162 => [
            0 => 'VariableStart',
            1 => '',
            2 => 52,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 52,
        ],
        164 => [
            0 => 'Dot',
            1 => '.',
            2 => 52,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 52,
        ],
        166 => [
            0 => 'Pipe',
            1 => '|',
            2 => 52,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 52,
        ],
        168 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 52,
        ],
        169 => [
            0 => 'TextData',
            1 => '
                </strong>
              </span>
            </p>
          </div>
        </div>
        </form>
      </li>
      ',
            2 => 52,
        ],
        170 => [
            0 => 'BlockStart',
            1 => '',
            2 => 60,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 60,
        ],
        172 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 60,
        ],
        173 => [
            0 => 'TextData',
            1 => '

    </ul>

    <div id="one-two">
      <div id="two">
        <h3>Why Shop With Us?</h3>
        <ul>
          <li class="two-a">
            <h4>24 Hours</h4>
            <p>We\'re always here to help.</p>
          </li>
          <li class="two-c">
            <h4>No Spam</h4>
            <p>We\'ll never share your info.</p>
          </li>
          <li class="two-b">
            <h4>Save Energy</h4>
            <p>We\'re green, all the way.</p>
          </li>
          <li class="two-d">
            <h4>Secure Servers</h4>
            <p>Checkout is 256bits encrypted.</p>
          </li>
        </ul>
      </div>

      <div id="one">
        <h3>Our Company</h3>
        ',
            2 => 60,
        ],
        174 => [
            0 => 'VariableStart',
            1 => '',
            2 => 89,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 89,
        ],
        176 => [
            0 => 'Dot',
            1 => '.',
            2 => 89,
        ],
        177 => [
            0 => 'Identifier',
            1 => 'about-us',
            2 => 89,
        ],
        178 => [
            0 => 'Dot',
            1 => '.',
            2 => 89,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 89,
        ],
        180 => [
            0 => 'Pipe',
            1 => '|',
            2 => 89,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 89,
        ],
        182 => [
            0 => 'Colon',
            1 => ':',
            2 => 89,
        ],
        183 => [
            0 => 'Number',
            1 => '49',
            2 => 89,
        ],
        184 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 89,
        ],
        185 => [
            0 => 'TextData',
            1 => ' <a href="/pages/about-us">read more</a></p>
      </div>
    </div>

  </div>
  <!-- end page -->
',
            2 => 89,
        ],
    ],
    'performance/tests/tribble/page.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="page" class="innerpage clearfix">

    <div id="text-page">
      <div class="entry">
        <h1>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 5,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 5,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h1>
        <div class="entry-post">
          ',
            2 => 5,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 7,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 7,
        ],
        11 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        12 => [
            0 => 'TextData',
            1 => '
        </div>
      </div>
    </div>


    <h1>Featured Products</h1>
    <ul class="item-list clearfix">

      ',
            2 => 7,
        ],
        13 => [
            0 => 'BlockStart',
            1 => '',
            2 => 16,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 16,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 16,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 16,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 16,
        ],
        18 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 16,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 16,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 16,
        ],
        22 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 16,
        ],
        23 => [
            0 => 'TextData',
            1 => '
      <li>
        <form action="/cart/add" method="post">
        <div class="item-list-item">
          <div class="ili-top clearfix">
            <div class="ili-top-content">
              <h2><a href="',
            2 => 16,
        ],
        24 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 22,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 22,
        ],
        28 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        29 => [
            0 => 'TextData',
            1 => '">',
            2 => 22,
        ],
        30 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 22,
        ],
        32 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 22,
        ],
        34 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        35 => [
            0 => 'TextData',
            1 => '</a></h2>
              <p>',
            2 => 22,
        ],
        36 => [
            0 => 'VariableStart',
            1 => '',
            2 => 23,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 23,
        ],
        38 => [
            0 => 'Dot',
            1 => '.',
            2 => 23,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 23,
        ],
        40 => [
            0 => 'Pipe',
            1 => '|',
            2 => 23,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 23,
        ],
        42 => [
            0 => 'Colon',
            1 => ':',
            2 => 23,
        ],
        43 => [
            0 => 'Number',
            1 => '15',
            2 => 23,
        ],
        44 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 23,
        ],
        45 => [
            0 => 'TextData',
            1 => '</p>
            </div>
            <a href="',
            2 => 23,
        ],
        46 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        48 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 25,
        ],
        50 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        51 => [
            0 => 'TextData',
            1 => '" class="ili-top-image"><img src="',
            2 => 25,
        ],
        52 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        54 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'featured_image',
            2 => 25,
        ],
        56 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 25,
        ],
        58 => [
            0 => 'Colon',
            1 => ':',
            2 => 25,
        ],
        59 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 25,
        ],
        60 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        61 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 25,
        ],
        62 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 25,
        ],
        64 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 25,
        ],
        66 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 25,
        ],
        68 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        69 => [
            0 => 'TextData',
            1 => '"/></a>
          </div>

          <div class="ili-bottom clearfix">
            <p class="hiddenvariants" style="display: none">',
            2 => 25,
        ],
        70 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 29,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 29,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 29,
        ],
        75 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 29,
        ],
        77 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        78 => [
            0 => 'TextData',
            1 => '<span><input type="radio" name="id" value="',
            2 => 29,
        ],
        79 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        81 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        83 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        84 => [
            0 => 'TextData',
            1 => '" id="radio_',
            2 => 29,
        ],
        85 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        87 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        89 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        90 => [
            0 => 'TextData',
            1 => '" style="vertical-align: middle;" ',
            2 => 29,
        ],
        91 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 29,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 29,
        ],
        94 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 29,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        97 => [
            0 => 'TextData',
            1 => ' checked="checked" ',
            2 => 29,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 29,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        101 => [
            0 => 'TextData',
            1 => ' /><label for="radio_',
            2 => 29,
        ],
        102 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        104 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 29,
        ],
        106 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        107 => [
            0 => 'TextData',
            1 => '">',
            2 => 29,
        ],
        108 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        110 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 29,
        ],
        112 => [
            0 => 'Pipe',
            1 => '|',
            2 => 29,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 29,
        ],
        114 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        115 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 29,
        ],
        116 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 29,
        ],
        118 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 29,
        ],
        120 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        121 => [
            0 => 'TextData',
            1 => '</label></span>',
            2 => 29,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 29,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        125 => [
            0 => 'TextData',
            1 => '</p>
            <input type="submit" class="" value="Add to Basket" />
            <p>
              <a href="',
            2 => 29,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 32,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 32,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 32,
        ],
        130 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 32,
        ],
        131 => [
            0 => 'TextData',
            1 => '">View Details</a>

              <span>
                ',
            2 => 32,
        ],
        132 => [
            0 => 'BlockStart',
            1 => '',
            2 => 35,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 35,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 35,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 35,
        ],
        137 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 35,
        ],
        138 => [
            0 => 'TextData',
            1 => '
                  ',
            2 => 35,
        ],
        139 => [
            0 => 'BlockStart',
            1 => '',
            2 => 36,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 36,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 36,
        ],
        144 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 36,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 36,
        ],
        146 => [
            0 => 'Dot',
            1 => '.',
            2 => 36,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 36,
        ],
        148 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 36,
        ],
        149 => [
            0 => 'TextData',
            1 => '
                    ',
            2 => 36,
        ],
        150 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 37,
        ],
        152 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 37,
        ],
        154 => [
            0 => 'Pipe',
            1 => '|',
            2 => 37,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 37,
        ],
        156 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        157 => [
            0 => 'TextData',
            1 => ' -
                    ',
            2 => 37,
        ],
        158 => [
            0 => 'BlockStart',
            1 => '',
            2 => 38,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 38,
        ],
        160 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 38,
        ],
        161 => [
            0 => 'TextData',
            1 => '
                ',
            2 => 38,
        ],
        162 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 39,
        ],
        164 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        165 => [
            0 => 'TextData',
            1 => '
                <strong>
                  ',
            2 => 39,
        ],
        166 => [
            0 => 'VariableStart',
            1 => '',
            2 => 41,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 41,
        ],
        168 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'price_min',
            2 => 41,
        ],
        170 => [
            0 => 'Pipe',
            1 => '|',
            2 => 41,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 41,
        ],
        172 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 41,
        ],
        173 => [
            0 => 'TextData',
            1 => '
                </strong>
              </span>
            </p>
          </div>
        </div>
        </form>
      </li>
      ',
            2 => 41,
        ],
        174 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 49,
        ],
        176 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        177 => [
            0 => 'TextData',
            1 => '

    </ul>
  </div>
  <!-- end page -->



',
            2 => 49,
        ],
    ],
    'performance/tests/tribble/product.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="page" class="innerpage clearfix">
  <h1>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => ' ',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 2,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        11 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        12 => [
            0 => 'TextData',
            1 => '</h1>


  <p class="latest-news"><strong>Product Tags: </strong>
    ',
            2 => 2,
        ],
        13 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 6,
        ],
        15 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 6,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 6,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        18 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 6,
        ],
        20 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        21 => [
            0 => 'TextData',
            1 => '
            <a href="/collections/all/',
            2 => 6,
        ],
        22 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 7,
        ],
        24 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        25 => [
            0 => 'TextData',
            1 => '">',
            2 => 7,
        ],
        26 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 7,
        ],
        28 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        29 => [
            0 => 'TextData',
            1 => '</a> |
          ',
            2 => 7,
        ],
        30 => [
            0 => 'BlockStart',
            1 => '',
            2 => 8,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 8,
        ],
        32 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 8,
        ],
        33 => [
            0 => 'TextData',
            1 => '
  </p>

  <div class="product clearfix">
    <div class="product-info">
      <h1>',
            2 => 8,
        ],
        34 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        36 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 13,
        ],
        38 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        39 => [
            0 => 'TextData',
            1 => '</h1>
      <div class="product-info-description">
        <p>',
            2 => 13,
        ],
        40 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        42 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 15,
        ],
        44 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        45 => [
            0 => 'TextData',
            1 => '  </p>
      </div>

      ',
            2 => 15,
        ],
        46 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 18,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'available',
            2 => 18,
        ],
        51 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        52 => [
            0 => 'TextData',
            1 => '
      <form action="/cart/add" method="post">

      <h2>Product Options:</h2>

      <select id="product-info-options" name="id" class="product-info-options">
        ',
            2 => 18,
        ],
        53 => [
            0 => 'BlockStart',
            1 => '',
            2 => 24,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 24,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 24,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 24,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 24,
        ],
        58 => [
            0 => 'Dot',
            1 => '.',
            2 => 24,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 24,
        ],
        60 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 24,
        ],
        61 => [
            0 => 'TextData',
            1 => '
          <option value="',
            2 => 24,
        ],
        62 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 25,
        ],
        64 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 25,
        ],
        66 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        67 => [
            0 => 'TextData',
            1 => '">',
            2 => 25,
        ],
        68 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 25,
        ],
        70 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 25,
        ],
        72 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        73 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 25,
        ],
        74 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        75 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 25,
        ],
        76 => [
            0 => 'Dot',
            1 => '.',
            2 => 25,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 25,
        ],
        78 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 25,
        ],
        80 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        81 => [
            0 => 'TextData',
            1 => '</option>
        ',
            2 => 25,
        ],
        82 => [
            0 => 'BlockStart',
            1 => '',
            2 => 26,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 26,
        ],
        84 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 26,
        ],
        85 => [
            0 => 'TextData',
            1 => '
      </select>

      <div id="price-field"></div>

      <div class="product-purchase-btn">
        <input type="submit" class="add-this-to-cart" id="add-this-to-cart" value="Add to Basket" />
      </div>

      </form>
      ',
            2 => 26,
        ],
        86 => [
            0 => 'BlockStart',
            1 => '',
            2 => 36,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 36,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 36,
        ],
        89 => [
            0 => 'TextData',
            1 => '
              <h2>Sold out!</h2>
              <p>Sorry, we\'re all out of this product. Check back often and order when it returns</p>
              ',
            2 => 36,
        ],
        90 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 39,
        ],
        92 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        93 => [
            0 => 'TextData',
            1 => '
    </div>

    <div class="product-images clearfix">
      ',
            2 => 39,
        ],
        94 => [
            0 => 'BlockStart',
            1 => '',
            2 => 43,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 43,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 43,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 43,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 43,
        ],
        99 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 43,
        ],
        101 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 43,
        ],
        102 => [
            0 => 'TextData',
            1 => '

      ',
            2 => 43,
        ],
        103 => [
            0 => 'BlockStart',
            1 => '',
            2 => 45,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 45,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 45,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 45,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 45,
        ],
        108 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 45,
        ],
        109 => [
            0 => 'TextData',
            1 => '
      <div class="product-image-large">
        <img src="',
            2 => 45,
        ],
        110 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 47,
        ],
        112 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 47,
        ],
        114 => [
            0 => 'Colon',
            1 => ':',
            2 => 47,
        ],
        115 => [
            0 => 'String',
            1 => '\'medium\'',
            2 => 47,
        ],
        116 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        117 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 47,
        ],
        118 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 47,
        ],
        120 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 47,
        ],
        122 => [
            0 => 'Pipe',
            1 => '|',
            2 => 47,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 47,
        ],
        124 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        125 => [
            0 => 'TextData',
            1 => '" />
      </div>
      ',
            2 => 47,
        ],
        126 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 49,
        ],
        128 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        129 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 49,
        ],
        130 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 50,
        ],
        132 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        133 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 50,
        ],
        134 => [
            0 => 'BlockStart',
            1 => '',
            2 => 51,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 51,
        ],
        136 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 51,
        ],
        137 => [
            0 => 'TextData',
            1 => '

      <ul class="product-thumbs clearfix">
      ',
            2 => 51,
        ],
        138 => [
            0 => 'BlockStart',
            1 => '',
            2 => 54,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 54,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 54,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 54,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 54,
        ],
        143 => [
            0 => 'Dot',
            1 => '.',
            2 => 54,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 54,
        ],
        145 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 54,
        ],
        146 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 54,
        ],
        147 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 55,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 55,
        ],
        150 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 55,
        ],
        152 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        153 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 55,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 56,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        157 => [
            0 => 'TextData',
            1 => '

      <li>
      <a href="',
            2 => 56,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 59,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 59,
        ],
        160 => [
            0 => 'Pipe',
            1 => '|',
            2 => 59,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 59,
        ],
        162 => [
            0 => 'Colon',
            1 => ':',
            2 => 59,
        ],
        163 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 59,
        ],
        164 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 59,
        ],
        165 => [
            0 => 'TextData',
            1 => '" class="product-thumbs" rel="lightbox[product]" title="">
        <img src="',
            2 => 59,
        ],
        166 => [
            0 => 'VariableStart',
            1 => '',
            2 => 60,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 60,
        ],
        168 => [
            0 => 'Pipe',
            1 => '|',
            2 => 60,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 60,
        ],
        170 => [
            0 => 'Colon',
            1 => ':',
            2 => 60,
        ],
        171 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 60,
        ],
        172 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 60,
        ],
        173 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 60,
        ],
        174 => [
            0 => 'VariableStart',
            1 => '',
            2 => 60,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 60,
        ],
        176 => [
            0 => 'Dot',
            1 => '.',
            2 => 60,
        ],
        177 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 60,
        ],
        178 => [
            0 => 'Pipe',
            1 => '|',
            2 => 60,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 60,
        ],
        180 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 60,
        ],
        181 => [
            0 => 'TextData',
            1 => '" />
      </a>
      </li>
      ',
            2 => 60,
        ],
        182 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        183 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 63,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        185 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 63,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 64,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 64,
        ],
        188 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 64,
        ],
        189 => [
            0 => 'TextData',
            1 => '
      </ul>
    </div>
  </div>



  <div id="three-reasons" class="clearfix">
    <h3>Why Shop With Us?</h3>
    <ul>
      <li class="two-a">
        <h4>24 Hours</h4>
        <p>We\'re always here to help.</p>
      </li>
      <li class="two-c">
        <h4>No Spam</h4>
        <p>We\'ll never share your info.</p>
      </li>
      <li class="two-d">
        <h4>Secure Servers</h4>
        <p>Checkout is 256bit encrypted.</p>
      </li>
    </ul>
  </div>

</div>
<!-- end page -->

<script type="text/javascript">
<!--
  // prototype callback for multi variants dropdown selector
  var selectCallback = function(variant, selector) {
    if (variant && variant.available == true) {
      // selected a valid variant
      $(\'add-this-to-cart\').removeClassName(\'disabled\'); // remove unavailable class from add-to-cart button
      $(\'add-this-to-cart\').disabled = false;           // reenable add-to-cart button
      $(\'price-field\').innerHTML = Shopify.formatMoney(variant.price, "',
            2 => 64,
        ],
        190 => [
            0 => 'VariableStart',
            1 => '',
            2 => 100,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 100,
        ],
        192 => [
            0 => 'Dot',
            1 => '.',
            2 => 100,
        ],
        193 => [
            0 => 'Identifier',
            1 => 'money_with_currency_format',
            2 => 100,
        ],
        194 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 100,
        ],
        195 => [
            0 => 'TextData',
            1 => '");  // update price field
    } else {
      // variant doesn\'t exist
      $(\'add-this-to-cart\').addClassName(\'disabled\');      // set add-to-cart button to unavailable class
      $(\'add-this-to-cart\').disabled = true;              // disable add-to-cart button
      $(\'price-field\').innerHTML = (variant) ? "Sold Out" : "Unavailable"; // update price-field message
    }
  };

  // initialize multi selector for product
  Event.observe(document, \'dom:loaded\', function() {
    new Shopify.OptionSelectors("product-info-options", { product: ',
            2 => 100,
        ],
        196 => [
            0 => 'VariableStart',
            1 => '',
            2 => 111,
        ],
        197 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 111,
        ],
        198 => [
            0 => 'Pipe',
            1 => '|',
            2 => 111,
        ],
        199 => [
            0 => 'Identifier',
            1 => 'json',
            2 => 111,
        ],
        200 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 111,
        ],
        201 => [
            0 => 'TextData',
            1 => ', onVariantSelected: selectCallback });
  });
-->
</script>


',
            2 => 111,
        ],
    ],
    'performance/tests/tribble/search.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '



    <div id="page" class="innerpage clearfix">
    <h1>Search Results</h1>
    ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 7,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 7,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 7,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'performed',
            2 => 7,
        ],
        6 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 7,
        ],
        7 => [
            0 => 'TextData',
            1 => '

     ',
            2 => 7,
        ],
        8 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 9,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 9,
        ],
        11 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'results',
            2 => 9,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 9,
        ],
        14 => [
            0 => 'Number',
            1 => '10',
            2 => 9,
        ],
        15 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        16 => [
            0 => 'TextData',
            1 => '

     ',
            2 => 9,
        ],
        17 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 11,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 11,
        ],
        20 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        21 => [
            0 => 'Identifier',
            1 => 'results',
            2 => 11,
        ],
        22 => [
            0 => 'Comparison',
            1 => '==',
            2 => 11,
        ],
        23 => [
            0 => 'Identifier',
            1 => 'empty',
            2 => 11,
        ],
        24 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        25 => [
            0 => 'TextData',
            1 => '
      <div class="latest-news">Your search for "',
            2 => 11,
        ],
        26 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 12,
        ],
        28 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'terms',
            2 => 12,
        ],
        30 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 12,
        ],
        32 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        33 => [
            0 => 'TextData',
            1 => '" did not yield any results</div>
        ',
            2 => 12,
        ],
        34 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 13,
        ],
        36 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        37 => [
            0 => 'TextData',
            1 => '


    <ul class="search-list clearfix">
     ',
            2 => 13,
        ],
        38 => [
            0 => 'BlockStart',
            1 => '',
            2 => 17,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 17,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 17,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 17,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 17,
        ],
        43 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'results',
            2 => 17,
        ],
        45 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 17,
        ],
        46 => [
            0 => 'TextData',
            1 => '
      <li>
          <h3 class="stitle">',
            2 => 17,
        ],
        47 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 19,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 19,
        ],
        51 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 19,
        ],
        53 => [
            0 => 'Colon',
            1 => ':',
            2 => 19,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 19,
        ],
        55 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 19,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        58 => [
            0 => 'TextData',
            1 => '</h3>
          <p class="sinfo">',
            2 => 19,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 20,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 20,
        ],
        61 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 20,
        ],
        63 => [
            0 => 'Pipe',
            1 => '|',
            2 => 20,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 20,
        ],
        65 => [
            0 => 'Pipe',
            1 => '|',
            2 => 20,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 20,
        ],
        67 => [
            0 => 'Colon',
            1 => ':',
            2 => 20,
        ],
        68 => [
            0 => 'Number',
            1 => '65',
            2 => 20,
        ],
        69 => [
            0 => 'Pipe',
            1 => '|',
            2 => 20,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'highlight',
            2 => 20,
        ],
        71 => [
            0 => 'Colon',
            1 => ':',
            2 => 20,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'search',
            2 => 20,
        ],
        73 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'terms',
            2 => 20,
        ],
        75 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 20,
        ],
        76 => [
            0 => 'TextData',
            1 => ' ... <a href="',
            2 => 20,
        ],
        77 => [
            0 => 'VariableStart',
            1 => '',
            2 => 20,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 20,
        ],
        79 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 20,
        ],
        81 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 20,
        ],
        82 => [
            0 => 'TextData',
            1 => '" title="">view this item</a></p>
      </li>
    ',
            2 => 20,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 22,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 22,
        ],
        85 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 22,
        ],
        86 => [
            0 => 'TextData',
            1 => '
    </ul>
     ',
            2 => 22,
        ],
        87 => [
            0 => 'BlockStart',
            1 => '',
            2 => 24,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 24,
        ],
        89 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 24,
        ],
        90 => [
            0 => 'TextData',
            1 => '

    <div class="paginate clearfix">
      ',
            2 => 24,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 27,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 27,
        ],
        93 => [
            0 => 'Pipe',
            1 => '|',
            2 => 27,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 27,
        ],
        95 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 27,
        ],
        96 => [
            0 => 'TextData',
            1 => '
    </div>


    <div id="three-reasons" class="clearfix">
      <h3>Why Shop With Us?</h3>
      <ul>
        <li class="two-a">
          <h4>24 Hours</h4>
          <p>We\'re always here to help.</p>
        </li>
        <li class="two-c">
          <h4>No Spam</h4>
          <p>We\'ll never share your info.</p>
        </li>
        <li class="two-d">
          <h4>Secure Servers</h4>
          <p>Checkout is 256bit encrypted.</p>
        </li>
      </ul>
    </div>
  </div>

',
            2 => 27,
        ],
        97 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 50,
        ],
        99 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        100 => [
            0 => 'TextData',
            1 => '
',
            2 => 50,
        ],
        101 => [
            0 => 'BlockStart',
            1 => '',
            2 => 51,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 51,
        ],
        103 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 51,
        ],
        104 => [
            0 => 'TextData',
            1 => '
',
            2 => 51,
        ],
    ],
    'performance/tests/tribble/theme.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 4,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 4,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        6 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 4,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page_title',
            2 => 4,
        ],
        9 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        10 => [
            0 => 'TextData',
            1 => '</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  ',
            2 => 4,
        ],
        11 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        12 => [
            0 => 'String',
            1 => '\'reset.css\'',
            2 => 7,
        ],
        13 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 7,
        ],
        15 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 7,
        ],
        17 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        18 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 7,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        20 => [
            0 => 'String',
            1 => '\'style.css\'',
            2 => 8,
        ],
        21 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 8,
        ],
        23 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 8,
        ],
        25 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        26 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 8,
        ],
        27 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        28 => [
            0 => 'String',
            1 => '\'lightbox.css\'',
            2 => 10,
        ],
        29 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 10,
        ],
        31 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 10,
        ],
        33 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        34 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 10,
        ],
        35 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        36 => [
            0 => 'String',
            1 => '\'prototype/1.6/prototype.js\'',
            2 => 11,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 11,
        ],
        39 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 11,
        ],
        41 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        42 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 11,
        ],
        43 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        44 => [
            0 => 'String',
            1 => '\'scriptaculous/1.8.2/scriptaculous.js\'',
            2 => 12,
        ],
        45 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 12,
        ],
        47 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 12,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        50 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 12,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 13,
        ],
        52 => [
            0 => 'String',
            1 => '\'lightbox.js\'',
            2 => 13,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 13,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 13,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 13,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 13,
        ],
        58 => [
            0 => 'TextData',
            1 => '
  ',
            2 => 13,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        60 => [
            0 => 'String',
            1 => '\'option_selection.js\'',
            2 => 14,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 14,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'shopify_asset_url',
            2 => 14,
        ],
        63 => [
            0 => 'Pipe',
            1 => '|',
            2 => 14,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 14,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        66 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 14,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'content_for_header',
            2 => 16,
        ],
        69 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        70 => [
            0 => 'TextData',
            1 => '
</head>
<body id="page-',
            2 => 16,
        ],
        71 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 18,
        ],
        73 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        74 => [
            0 => 'TextData',
            1 => '">

<div id="wrap">

  <div id="top">
    <div id="cart">
      <h3>Shopping Cart</h3>
      <p class="cart-count">
        ',
            2 => 18,
        ],
        75 => [
            0 => 'BlockStart',
            1 => '',
            2 => 26,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 26,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 26,
        ],
        78 => [
            0 => 'Dot',
            1 => '.',
            2 => 26,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 26,
        ],
        80 => [
            0 => 'Comparison',
            1 => '==',
            2 => 26,
        ],
        81 => [
            0 => 'Number',
            1 => '0',
            2 => 26,
        ],
        82 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 26,
        ],
        83 => [
            0 => 'TextData',
            1 => '
          Your cart is currently empty
        ',
            2 => 26,
        ],
        84 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 28,
        ],
        86 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        87 => [
            0 => 'TextData',
            1 => '
          ',
            2 => 28,
        ],
        88 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 29,
        ],
        90 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 29,
        ],
        92 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        93 => [
            0 => 'TextData',
            1 => ' ',
            2 => 29,
        ],
        94 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 29,
        ],
        96 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        97 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 29,
        ],
        98 => [
            0 => 'Pipe',
            1 => '|',
            2 => 29,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 29,
        ],
        100 => [
            0 => 'Colon',
            1 => ':',
            2 => 29,
        ],
        101 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 29,
        ],
        102 => [
            0 => 'Comma',
            1 => ',',
            2 => 29,
        ],
        103 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 29,
        ],
        104 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        105 => [
            0 => 'TextData',
            1 => ' <span>-</span> Total: ',
            2 => 29,
        ],
        106 => [
            0 => 'VariableStart',
            1 => '',
            2 => 29,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 29,
        ],
        108 => [
            0 => 'Dot',
            1 => '.',
            2 => 29,
        ],
        109 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 29,
        ],
        110 => [
            0 => 'Pipe',
            1 => '|',
            2 => 29,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'money_with_currency',
            2 => 29,
        ],
        112 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 29,
        ],
        113 => [
            0 => 'TextData',
            1 => ' <span>-</span> <a href="/cart">View Cart</a>
        ',
            2 => 29,
        ],
        114 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 30,
        ],
        116 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        117 => [
            0 => 'TextData',
            1 => '
      </p>
    </div>

    <div id="site-title">
      <h3><a href="/">',
            2 => 30,
        ],
        118 => [
            0 => 'VariableStart',
            1 => '',
            2 => 35,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 35,
        ],
        120 => [
            0 => 'Dot',
            1 => '.',
            2 => 35,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 35,
        ],
        122 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 35,
        ],
        123 => [
            0 => 'TextData',
            1 => '</a></h3>
      <h4><span>Tribble: A Shopify Theme</span></h4>

    </div>
  </div>

  <ul id="nav">
    ',
            2 => 35,
        ],
        124 => [
            0 => 'BlockStart',
            1 => '',
            2 => 42,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 42,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 42,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 42,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 42,
        ],
        129 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 42,
        ],
        131 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 42,
        ],
        133 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 42,
        ],
        134 => [
            0 => 'TextData',
            1 => '
     <li>',
            2 => 42,
        ],
        135 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 43,
        ],
        137 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        138 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 43,
        ],
        139 => [
            0 => 'Pipe',
            1 => '|',
            2 => 43,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 43,
        ],
        141 => [
            0 => 'Colon',
            1 => ':',
            2 => 43,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 43,
        ],
        143 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 43,
        ],
        145 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        146 => [
            0 => 'TextData',
            1 => '</li>
    ',
            2 => 43,
        ],
        147 => [
            0 => 'BlockStart',
            1 => '',
            2 => 44,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 44,
        ],
        149 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 44,
        ],
        150 => [
            0 => 'TextData',
            1 => '
  </ul>

  ',
            2 => 44,
        ],
        151 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        152 => [
            0 => 'Identifier',
            1 => 'content_for_layout',
            2 => 47,
        ],
        153 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        154 => [
            0 => 'TextData',
            1 => '

  <div id="foot" class="clearfix">
    <div class="quick-links">
      <h4>Quick Navigation</h4>
      <ul class="clearfix">
        <li><a href="/">Home</a></li>
        <li><a href="#top">Back to top</a></li>
        ',
            2 => 47,
        ],
        155 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        156 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 55,
        ],
        157 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 55,
        ],
        158 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 55,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 55,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 55,
        ],
        162 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 55,
        ],
        164 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        165 => [
            0 => 'TextData',
            1 => '
         <li>',
            2 => 55,
        ],
        166 => [
            0 => 'VariableStart',
            1 => '',
            2 => 56,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 56,
        ],
        168 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 56,
        ],
        170 => [
            0 => 'Pipe',
            1 => '|',
            2 => 56,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 56,
        ],
        172 => [
            0 => 'Colon',
            1 => ':',
            2 => 56,
        ],
        173 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 56,
        ],
        174 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        175 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 56,
        ],
        176 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 56,
        ],
        177 => [
            0 => 'TextData',
            1 => '</li>
        ',
            2 => 56,
        ],
        178 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 57,
        ],
        180 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        181 => [
            0 => 'TextData',
            1 => '
      </ul>
    </div>

    <div class="quick-contact">
      <h4>Quick Contact</h4>
      <div class="vcard">

          <div class="org fn">
             <div class="organization-name">Really Great Widget Co.</div>
          </div>
        <div class="adr">
            <span class="street-address">2531 Barrington Court</span>
            <span class="locality">Hayward</span>,
          <abbr title="California" class="region">CA</abbr>
            <span class="postal-code">94545</span>
         </div>
        <a class="email" href="mailto:email@myshopifysite.com">
          email@myshopifysite.com
        </a>
        <div class="tel">
          <span class="type">Support:</span> <span class="value">800-555-9954</span>
        </div>
      </div>

    </div>

    <p><a href="http://shopify.com" class="we-made">Powered by Shopify</a> &copy; Copyright ',
            2 => 57,
        ],
        182 => [
            0 => 'VariableStart',
            1 => '',
            2 => 84,
        ],
        183 => [
            0 => 'String',
            1 => '"now"',
            2 => 84,
        ],
        184 => [
            0 => 'Pipe',
            1 => '|',
            2 => 84,
        ],
        185 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 84,
        ],
        186 => [
            0 => 'Colon',
            1 => ':',
            2 => 84,
        ],
        187 => [
            0 => 'String',
            1 => '"%Y"',
            2 => 84,
        ],
        188 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 84,
        ],
        189 => [
            0 => 'TextData',
            1 => ' ',
            2 => 84,
        ],
        190 => [
            0 => 'VariableStart',
            1 => '',
            2 => 84,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 84,
        ],
        192 => [
            0 => 'Dot',
            1 => '.',
            2 => 84,
        ],
        193 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 84,
        ],
        194 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 84,
        ],
        195 => [
            0 => 'TextData',
            1 => ', All Rights Reserved.  <a href="/blogs/news.xml" id="foot-rss">RSS Feed</a></p>
  </div>

</div>

</body>
</html>
',
            2 => 84,
        ],
    ],
    'performance/tests/vogue/article.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div class="article">
  <h3 class="article-head-title">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h3>
  <p> posted ',
            2 => 2,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 3,
        ],
        11 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 3,
        ],
        13 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        14 => [
            0 => 'String',
            1 => '"%Y %h"',
            2 => 3,
        ],
        15 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        16 => [
            0 => 'TextData',
            1 => ' by ',
            2 => 3,
        ],
        17 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        19 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 3,
        ],
        21 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        22 => [
            0 => 'TextData',
            1 => '</p>
  <div class="article-body textile">
    ',
            2 => 3,
        ],
        23 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 5,
        ],
        25 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 5,
        ],
        27 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        28 => [
            0 => 'TextData',
            1 => '
  </div>
</div>

',
            2 => 5,
        ],
        29 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 9,
        ],
        31 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 9,
        ],
        32 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 9,
        ],
        34 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        35 => [
            0 => 'TextData',
            1 => '
<div id="comments">
  <h3>Comments</h3>
  <!-- List all comments -->

  <ul>
  ',
            2 => 9,
        ],
        36 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 15,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 15,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 15,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 15,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'comments',
            2 => 15,
        ],
        43 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        44 => [
            0 => 'TextData',
            1 => '
    <li>
      <div class="comment">
        ',
            2 => 15,
        ],
        45 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 18,
        ],
        47 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 18,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        50 => [
            0 => 'TextData',
            1 => '
      </div>

      <div class="comment-details">
        Posted by ',
            2 => 18,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 22,
        ],
        53 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 22,
        ],
        55 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        56 => [
            0 => 'TextData',
            1 => ' on ',
            2 => 22,
        ],
        57 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'comment',
            2 => 22,
        ],
        59 => [
            0 => 'Dot',
            1 => '.',
            2 => 22,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 22,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 22,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 22,
        ],
        63 => [
            0 => 'Colon',
            1 => ':',
            2 => 22,
        ],
        64 => [
            0 => 'String',
            1 => '"%B %d, %Y"',
            2 => 22,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        66 => [
            0 => 'TextData',
            1 => '
      </div>
    </li>
  ',
            2 => 22,
        ],
        67 => [
            0 => 'BlockStart',
            1 => '',
            2 => 25,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 25,
        ],
        69 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 25,
        ],
        70 => [
            0 => 'TextData',
            1 => '
  </ul>

  <!-- Comment Form -->
  ',
            2 => 25,
        ],
        71 => [
            0 => 'BlockStart',
            1 => '',
            2 => 29,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 29,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 29,
        ],
        74 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 29,
        ],
        75 => [
            0 => 'TextData',
            1 => '
    <h3>Leave a comment</h3>

    <!-- Check if a comment has been submitted in the last request, and if yes display an appropriate message -->
    ',
            2 => 29,
        ],
        76 => [
            0 => 'BlockStart',
            1 => '',
            2 => 33,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 33,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 33,
        ],
        79 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'posted_successfully?',
            2 => 33,
        ],
        81 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 33,
        ],
        82 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 33,
        ],
        83 => [
            0 => 'BlockStart',
            1 => '',
            2 => 34,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 34,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 34,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 34,
        ],
        88 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 34,
        ],
        89 => [
            0 => 'TextData',
            1 => '
        <div class="notice">
          Successfully posted your comment.<br />
          It will have to be approved by the blog owner first before showing up.
        </div>
      ',
            2 => 34,
        ],
        90 => [
            0 => 'BlockStart',
            1 => '',
            2 => 39,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 39,
        ],
        92 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 39,
        ],
        93 => [
            0 => 'TextData',
            1 => '
        <div class="notice">Successfully posted your comment.</div>
      ',
            2 => 39,
        ],
        94 => [
            0 => 'BlockStart',
            1 => '',
            2 => 41,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 41,
        ],
        96 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 41,
        ],
        97 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 41,
        ],
        98 => [
            0 => 'BlockStart',
            1 => '',
            2 => 42,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 42,
        ],
        100 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 42,
        ],
        101 => [
            0 => 'TextData',
            1 => '

    ',
            2 => 42,
        ],
        102 => [
            0 => 'BlockStart',
            1 => '',
            2 => 44,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 44,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 44,
        ],
        105 => [
            0 => 'Dot',
            1 => '.',
            2 => 44,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 44,
        ],
        107 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 44,
        ],
        108 => [
            0 => 'TextData',
            1 => '
      <div class="notice error">Not all the fields have been filled out correctly!</div>
    ',
            2 => 44,
        ],
        109 => [
            0 => 'BlockStart',
            1 => '',
            2 => 46,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 46,
        ],
        111 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 46,
        ],
        112 => [
            0 => 'TextData',
            1 => '

    <dl>
      <dt class="',
            2 => 46,
        ],
        113 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 49,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 49,
        ],
        116 => [
            0 => 'Dot',
            1 => '.',
            2 => 49,
        ],
        117 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 49,
        ],
        118 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 49,
        ],
        119 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 49,
        ],
        120 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        121 => [
            0 => 'TextData',
            1 => 'error',
            2 => 49,
        ],
        122 => [
            0 => 'BlockStart',
            1 => '',
            2 => 49,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 49,
        ],
        124 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 49,
        ],
        125 => [
            0 => 'TextData',
            1 => '"><label for="comment_author">Your name</label></dt>
      <dd><input type="text" id="comment_author" name="comment[author]" size="40" value="',
            2 => 49,
        ],
        126 => [
            0 => 'VariableStart',
            1 => '',
            2 => 50,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 50,
        ],
        128 => [
            0 => 'Dot',
            1 => '.',
            2 => 50,
        ],
        129 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 50,
        ],
        130 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 50,
        ],
        131 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 50,
        ],
        132 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 50,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 50,
        ],
        135 => [
            0 => 'Dot',
            1 => '.',
            2 => 50,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 50,
        ],
        137 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 50,
        ],
        138 => [
            0 => 'String',
            1 => '\'author\'',
            2 => 50,
        ],
        139 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        140 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 50,
        ],
        141 => [
            0 => 'BlockStart',
            1 => '',
            2 => 50,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 50,
        ],
        143 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 50,
        ],
        144 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 50,
        ],
        145 => [
            0 => 'BlockStart',
            1 => '',
            2 => 52,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 52,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 52,
        ],
        148 => [
            0 => 'Dot',
            1 => '.',
            2 => 52,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 52,
        ],
        150 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 52,
        ],
        151 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 52,
        ],
        152 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 52,
        ],
        153 => [
            0 => 'TextData',
            1 => 'error',
            2 => 52,
        ],
        154 => [
            0 => 'BlockStart',
            1 => '',
            2 => 52,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 52,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 52,
        ],
        157 => [
            0 => 'TextData',
            1 => '"><label for="comment_email">Your email</label></dt>
      <dd><input type="text" id="comment_email" name="comment[email]" size="40" value="',
            2 => 52,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 53,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 53,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 53,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'email',
            2 => 53,
        ],
        162 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 53,
        ],
        163 => [
            0 => 'TextData',
            1 => '" class="',
            2 => 53,
        ],
        164 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 53,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 53,
        ],
        167 => [
            0 => 'Dot',
            1 => '.',
            2 => 53,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 53,
        ],
        169 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 53,
        ],
        170 => [
            0 => 'String',
            1 => '\'email\'',
            2 => 53,
        ],
        171 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        172 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 53,
        ],
        173 => [
            0 => 'BlockStart',
            1 => '',
            2 => 53,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 53,
        ],
        175 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 53,
        ],
        176 => [
            0 => 'TextData',
            1 => '" /></dd>

      <dt class="',
            2 => 53,
        ],
        177 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        178 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 55,
        ],
        179 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 55,
        ],
        180 => [
            0 => 'Dot',
            1 => '.',
            2 => 55,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 55,
        ],
        182 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 55,
        ],
        183 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 55,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        185 => [
            0 => 'TextData',
            1 => 'error',
            2 => 55,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 55,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 55,
        ],
        188 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 55,
        ],
        189 => [
            0 => 'TextData',
            1 => '"><label for="comment_body">Your comment</label></dt>
      <dd><textarea id="comment_body" name="comment[body]" cols="40" rows="5" class="',
            2 => 55,
        ],
        190 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        191 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 56,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'errors',
            2 => 56,
        ],
        195 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 56,
        ],
        196 => [
            0 => 'String',
            1 => '\'body\'',
            2 => 56,
        ],
        197 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        198 => [
            0 => 'TextData',
            1 => 'input-error',
            2 => 56,
        ],
        199 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        201 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        202 => [
            0 => 'TextData',
            1 => '">',
            2 => 56,
        ],
        203 => [
            0 => 'VariableStart',
            1 => '',
            2 => 56,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'form',
            2 => 56,
        ],
        205 => [
            0 => 'Dot',
            1 => '.',
            2 => 56,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'body',
            2 => 56,
        ],
        207 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 56,
        ],
        208 => [
            0 => 'TextData',
            1 => '</textarea></dd>
    </dl>

    ',
            2 => 56,
        ],
        209 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        210 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 59,
        ],
        211 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 59,
        ],
        212 => [
            0 => 'Dot',
            1 => '.',
            2 => 59,
        ],
        213 => [
            0 => 'Identifier',
            1 => 'moderated?',
            2 => 59,
        ],
        214 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        215 => [
            0 => 'TextData',
            1 => '
      <p class="hint">comments have to be approved before showing up</p>
    ',
            2 => 59,
        ],
        216 => [
            0 => 'BlockStart',
            1 => '',
            2 => 61,
        ],
        217 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 61,
        ],
        218 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 61,
        ],
        219 => [
            0 => 'TextData',
            1 => '

    <input type="submit" value="Post comment" />
  ',
            2 => 61,
        ],
        220 => [
            0 => 'BlockStart',
            1 => '',
            2 => 64,
        ],
        221 => [
            0 => 'Identifier',
            1 => 'endform',
            2 => 64,
        ],
        222 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 64,
        ],
        223 => [
            0 => 'TextData',
            1 => '
</div>
',
            2 => 64,
        ],
        224 => [
            0 => 'BlockStart',
            1 => '',
            2 => 66,
        ],
        225 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 66,
        ],
        226 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 66,
        ],
        227 => [
            0 => 'TextData',
            1 => '
',
            2 => 66,
        ],
    ],
    'performance/tests/vogue/blog.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="shop-id-label_about">
<h3 class="article-head-title">',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 2,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h3>

',
            2 => 2,
        ],
        7 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 4,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 4,
        ],
        10 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 4,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 4,
        ],
        13 => [
            0 => 'Number',
            1 => '20',
            2 => 4,
        ],
        14 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        15 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 4,
        ],
        16 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 6,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 6,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 6,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 6,
        ],
        21 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 6,
        ],
        23 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        24 => [
            0 => 'TextData',
            1 => '
    <div class="article">
      <h3 class="article-head-title">
        <a href="',
            2 => 6,
        ],
        25 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 9,
        ],
        27 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 9,
        ],
        29 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        30 => [
            0 => 'TextData',
            1 => '">',
            2 => 9,
        ],
        31 => [
            0 => 'VariableStart',
            1 => '',
            2 => 9,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 9,
        ],
        33 => [
            0 => 'Dot',
            1 => '.',
            2 => 9,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 9,
        ],
        35 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 9,
        ],
        36 => [
            0 => 'TextData',
            1 => '</a>
      </h3>

      <p>
      ',
            2 => 9,
        ],
        37 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 13,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'blog',
            2 => 13,
        ],
        40 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'comments_enabled?',
            2 => 13,
        ],
        42 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        43 => [
            0 => 'TextData',
            1 => '
        <a href="',
            2 => 13,
        ],
        44 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 14,
        ],
        46 => [
            0 => 'Dot',
            1 => '.',
            2 => 14,
        ],
        47 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 14,
        ],
        48 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        49 => [
            0 => 'TextData',
            1 => '#comments">',
            2 => 14,
        ],
        50 => [
            0 => 'VariableStart',
            1 => '',
            2 => 14,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 14,
        ],
        52 => [
            0 => 'Dot',
            1 => '.',
            2 => 14,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'comments_count',
            2 => 14,
        ],
        54 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 14,
        ],
        55 => [
            0 => 'TextData',
            1 => ' comments</a>
        &mdash;
      ',
            2 => 14,
        ],
        56 => [
            0 => 'BlockStart',
            1 => '',
            2 => 16,
        ],
        57 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 16,
        ],
        58 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 16,
        ],
        59 => [
            0 => 'TextData',
            1 => '
      posted ',
            2 => 16,
        ],
        60 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 17,
        ],
        62 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'created_at',
            2 => 17,
        ],
        64 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 17,
        ],
        66 => [
            0 => 'Colon',
            1 => ':',
            2 => 17,
        ],
        67 => [
            0 => 'String',
            1 => '"%Y %h"',
            2 => 17,
        ],
        68 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        69 => [
            0 => 'TextData',
            1 => ' by ',
            2 => 17,
        ],
        70 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 17,
        ],
        72 => [
            0 => 'Dot',
            1 => '.',
            2 => 17,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'author',
            2 => 17,
        ],
        74 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        75 => [
            0 => 'TextData',
            1 => '</p>
      <div class="article-body textile">
        ',
            2 => 17,
        ],
        76 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 19,
        ],
        78 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 19,
        ],
        80 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        81 => [
            0 => 'TextData',
            1 => '
      </div>
    </div>
  ',
            2 => 19,
        ],
        82 => [
            0 => 'BlockStart',
            1 => '',
            2 => 22,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 22,
        ],
        84 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 22,
        ],
        85 => [
            0 => 'TextData',
            1 => '

  <div id="pagination">
    ',
            2 => 22,
        ],
        86 => [
            0 => 'VariableStart',
            1 => '',
            2 => 25,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 25,
        ],
        88 => [
            0 => 'Pipe',
            1 => '|',
            2 => 25,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 25,
        ],
        90 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 25,
        ],
        91 => [
            0 => 'TextData',
            1 => '
  </div>

',
            2 => 25,
        ],
        92 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        93 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 28,
        ],
        94 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        95 => [
            0 => 'TextData',
            1 => '


</div>
<div class="clear-me"></div>
',
            2 => 28,
        ],
    ],
    'performance/tests/vogue/cart.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<h1>Shopping Cart</h1>
',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 2,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 2,
        ],
        4 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 2,
        ],
        6 => [
            0 => 'Comparison',
            1 => '==',
            2 => 2,
        ],
        7 => [
            0 => 'Number',
            1 => '0',
            2 => 2,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 2,
        ],
        9 => [
            0 => 'TextData',
            1 => '
  <p><strong>Your shopping basket is empty.</strong> Perhaps a featured item below is of interest...</p>
  <table id="gallery">
  ',
            2 => 2,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 5,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'tablerow',
            2 => 5,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 5,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 5,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 5,
        ],
        15 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 5,
        ],
        17 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        18 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 5,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'cols',
            2 => 5,
        ],
        20 => [
            0 => 'Colon',
            1 => ':',
            2 => 5,
        ],
        21 => [
            0 => 'Number',
            1 => '3',
            2 => 5,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 5,
        ],
        23 => [
            0 => 'Colon',
            1 => ':',
            2 => 5,
        ],
        24 => [
            0 => 'Number',
            1 => '12',
            2 => 5,
        ],
        25 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 5,
        ],
        26 => [
            0 => 'TextData',
            1 => '
    <div class="gallery-image">
      <a href="',
            2 => 5,
        ],
        27 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        29 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 7,
        ],
        31 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 7,
        ],
        33 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        34 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 7,
        ],
        35 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 7,
        ],
        37 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        38 => [
            0 => 'TextData',
            1 => '" title="',
            2 => 7,
        ],
        39 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        41 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 7,
        ],
        43 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 7,
        ],
        45 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        46 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 7,
        ],
        47 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 7,
        ],
        51 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 7,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 7,
        ],
        55 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        56 => [
            0 => 'Number',
            1 => '50',
            2 => 7,
        ],
        57 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 7,
        ],
        59 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        60 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 7,
        ],
        61 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        63 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 7,
        ],
        65 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 7,
        ],
        67 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 7,
        ],
        69 => [
            0 => 'Colon',
            1 => ':',
            2 => 7,
        ],
        70 => [
            0 => 'String',
            1 => '\'medium\'',
            2 => 7,
        ],
        71 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        72 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 7,
        ],
        73 => [
            0 => 'VariableStart',
            1 => '',
            2 => 7,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 7,
        ],
        75 => [
            0 => 'Dot',
            1 => '.',
            2 => 7,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 7,
        ],
        77 => [
            0 => 'Pipe',
            1 => '|',
            2 => 7,
        ],
        78 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 7,
        ],
        79 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 7,
        ],
        80 => [
            0 => 'TextData',
            1 => '" /></a>
    </div>
    <div class="gallery-info">
      <a href="',
            2 => 7,
        ],
        81 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        83 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 10,
        ],
        85 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 10,
        ],
        87 => [
            0 => 'Colon',
            1 => ':',
            2 => 10,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 10,
        ],
        89 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        90 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 10,
        ],
        91 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        92 => [
            0 => 'TextData',
            1 => '">',
            2 => 10,
        ],
        93 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        95 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 10,
        ],
        97 => [
            0 => 'Pipe',
            1 => '|',
            2 => 10,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 10,
        ],
        99 => [
            0 => 'Colon',
            1 => ':',
            2 => 10,
        ],
        100 => [
            0 => 'Number',
            1 => '30',
            2 => 10,
        ],
        101 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        102 => [
            0 => 'TextData',
            1 => '</a><br />
      <small>',
            2 => 10,
        ],
        103 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        105 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 11,
        ],
        107 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 11,
        ],
        109 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        110 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 11,
        ],
        112 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        113 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 11,
        ],
        115 => [
            0 => 'Comparison',
            1 => '>',
            2 => 11,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        117 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        118 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 11,
        ],
        119 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        120 => [
            0 => 'TextData',
            1 => ' <del>',
            2 => 11,
        ],
        121 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        122 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        123 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        124 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 11,
        ],
        125 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 11,
        ],
        127 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        128 => [
            0 => 'TextData',
            1 => '</del>',
            2 => 11,
        ],
        129 => [
            0 => 'BlockStart',
            1 => '',
            2 => 11,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 11,
        ],
        131 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 11,
        ],
        132 => [
            0 => 'TextData',
            1 => '</small>
    </div>
  ',
            2 => 11,
        ],
        133 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'endtablerow',
            2 => 13,
        ],
        135 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        136 => [
            0 => 'TextData',
            1 => '
  </table>
',
            2 => 13,
        ],
        137 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        138 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 15,
        ],
        139 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        140 => [
            0 => 'TextData',
            1 => '
<script type="text/javascript">
  function remove_item(id) {
      document.getElementById(\'updates_\'+id).value = 0;
      document.getElementById(\'cartform\').submit();
  }
</script>
<form action="/cart" method="post" id="cartform">
  <table id="basket">
    <tr>
      <th>Item Description</th>
      <th>Price</th>
      <th>Qty</th>
      <th>Delete</th>
      <th>Total</th>
    </tr>',
            2 => 15,
        ],
        141 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 30,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 30,
        ],
        144 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 30,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 30,
        ],
        146 => [
            0 => 'Dot',
            1 => '.',
            2 => 30,
        ],
        147 => [
            0 => 'Identifier',
            1 => 'items',
            2 => 30,
        ],
        148 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        149 => [
            0 => 'TextData',
            1 => '
    <tr class="basket-',
            2 => 30,
        ],
        150 => [
            0 => 'BlockStart',
            1 => '',
            2 => 31,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'cycle',
            2 => 31,
        ],
        152 => [
            0 => 'String',
            1 => '\'odd\'',
            2 => 31,
        ],
        153 => [
            0 => 'Comma',
            1 => ',',
            2 => 31,
        ],
        154 => [
            0 => 'String',
            1 => '\'even\'',
            2 => 31,
        ],
        155 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 31,
        ],
        156 => [
            0 => 'TextData',
            1 => '">
      <td class="basket-column-one">
        <div class="basket-images">
          <a href="',
            2 => 31,
        ],
        157 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        158 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        159 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        161 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 34,
        ],
        163 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        164 => [
            0 => 'TextData',
            1 => '" title="',
            2 => 34,
        ],
        165 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        167 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 34,
        ],
        169 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        170 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 34,
        ],
        171 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        172 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 34,
        ],
        173 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        175 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        176 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        177 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        178 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 34,
        ],
        179 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        180 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 34,
        ],
        181 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        182 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 34,
        ],
        183 => [
            0 => 'Colon',
            1 => ':',
            2 => 34,
        ],
        184 => [
            0 => 'Number',
            1 => '50',
            2 => 34,
        ],
        185 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        186 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 34,
        ],
        187 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        188 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 34,
        ],
        189 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        191 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 34,
        ],
        195 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        196 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 34,
        ],
        197 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        198 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 34,
        ],
        199 => [
            0 => 'Colon',
            1 => ':',
            2 => 34,
        ],
        200 => [
            0 => 'String',
            1 => '\'thumb\'',
            2 => 34,
        ],
        201 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        202 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 34,
        ],
        203 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        204 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 34,
        ],
        205 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        206 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 34,
        ],
        207 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        208 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 34,
        ],
        209 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        210 => [
            0 => 'TextData',
            1 => '" /></a>
        </div>
        <div class="basket-desc">
          <p><a href="',
            2 => 34,
        ],
        211 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        212 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 37,
        ],
        213 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        214 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 37,
        ],
        215 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        216 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 37,
        ],
        217 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        218 => [
            0 => 'TextData',
            1 => '">',
            2 => 37,
        ],
        219 => [
            0 => 'VariableStart',
            1 => '',
            2 => 37,
        ],
        220 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 37,
        ],
        221 => [
            0 => 'Dot',
            1 => '.',
            2 => 37,
        ],
        222 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 37,
        ],
        223 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 37,
        ],
        224 => [
            0 => 'TextData',
            1 => '</a></p>
          ',
            2 => 37,
        ],
        225 => [
            0 => 'VariableStart',
            1 => '',
            2 => 38,
        ],
        226 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 38,
        ],
        227 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        228 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 38,
        ],
        229 => [
            0 => 'Dot',
            1 => '.',
            2 => 38,
        ],
        230 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 38,
        ],
        231 => [
            0 => 'Pipe',
            1 => '|',
            2 => 38,
        ],
        232 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 38,
        ],
        233 => [
            0 => 'Pipe',
            1 => '|',
            2 => 38,
        ],
        234 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 38,
        ],
        235 => [
            0 => 'Colon',
            1 => ':',
            2 => 38,
        ],
        236 => [
            0 => 'Number',
            1 => '120',
            2 => 38,
        ],
        237 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 38,
        ],
        238 => [
            0 => 'TextData',
            1 => '
        </div>
      </td>
      <td class="basket-column">',
            2 => 38,
        ],
        239 => [
            0 => 'VariableStart',
            1 => '',
            2 => 41,
        ],
        240 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 41,
        ],
        241 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        242 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 41,
        ],
        243 => [
            0 => 'Pipe',
            1 => '|',
            2 => 41,
        ],
        244 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 41,
        ],
        245 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 41,
        ],
        246 => [
            0 => 'BlockStart',
            1 => '',
            2 => 41,
        ],
        247 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 41,
        ],
        248 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 41,
        ],
        249 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        250 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 41,
        ],
        251 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        252 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 41,
        ],
        253 => [
            0 => 'Comparison',
            1 => '>',
            2 => 41,
        ],
        254 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 41,
        ],
        255 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        256 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 41,
        ],
        257 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 41,
        ],
        258 => [
            0 => 'TextData',
            1 => '<br /><del>',
            2 => 41,
        ],
        259 => [
            0 => 'VariableStart',
            1 => '',
            2 => 41,
        ],
        260 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 41,
        ],
        261 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        262 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 41,
        ],
        263 => [
            0 => 'Dot',
            1 => '.',
            2 => 41,
        ],
        264 => [
            0 => 'Identifier',
            1 => 'compare_at_price',
            2 => 41,
        ],
        265 => [
            0 => 'Pipe',
            1 => '|',
            2 => 41,
        ],
        266 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 41,
        ],
        267 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 41,
        ],
        268 => [
            0 => 'TextData',
            1 => '</del>',
            2 => 41,
        ],
        269 => [
            0 => 'BlockStart',
            1 => '',
            2 => 41,
        ],
        270 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 41,
        ],
        271 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 41,
        ],
        272 => [
            0 => 'TextData',
            1 => '</td>
      <td class="basket-column"><input type="text" size="4" name="updates[',
            2 => 41,
        ],
        273 => [
            0 => 'VariableStart',
            1 => '',
            2 => 42,
        ],
        274 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 42,
        ],
        275 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        276 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 42,
        ],
        277 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        278 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 42,
        ],
        279 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 42,
        ],
        280 => [
            0 => 'TextData',
            1 => ']" id="updates_',
            2 => 42,
        ],
        281 => [
            0 => 'VariableStart',
            1 => '',
            2 => 42,
        ],
        282 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 42,
        ],
        283 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        284 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 42,
        ],
        285 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        286 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 42,
        ],
        287 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 42,
        ],
        288 => [
            0 => 'TextData',
            1 => '" value="',
            2 => 42,
        ],
        289 => [
            0 => 'VariableStart',
            1 => '',
            2 => 42,
        ],
        290 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 42,
        ],
        291 => [
            0 => 'Dot',
            1 => '.',
            2 => 42,
        ],
        292 => [
            0 => 'Identifier',
            1 => 'quantity',
            2 => 42,
        ],
        293 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 42,
        ],
        294 => [
            0 => 'TextData',
            1 => '" onfocus="this.select();"/></td>
      <td class="basket-column"><a href="#" onclick="remove_item(',
            2 => 42,
        ],
        295 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        296 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 43,
        ],
        297 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        298 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 43,
        ],
        299 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        300 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 43,
        ],
        301 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        302 => [
            0 => 'TextData',
            1 => '); return false;">Remove</a></td>
      <td class="basket-column">',
            2 => 43,
        ],
        303 => [
            0 => 'VariableStart',
            1 => '',
            2 => 44,
        ],
        304 => [
            0 => 'Identifier',
            1 => 'item',
            2 => 44,
        ],
        305 => [
            0 => 'Dot',
            1 => '.',
            2 => 44,
        ],
        306 => [
            0 => 'Identifier',
            1 => 'line_price',
            2 => 44,
        ],
        307 => [
            0 => 'Pipe',
            1 => '|',
            2 => 44,
        ],
        308 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 44,
        ],
        309 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 44,
        ],
        310 => [
            0 => 'TextData',
            1 => '</td>
    </tr>',
            2 => 44,
        ],
        311 => [
            0 => 'BlockStart',
            1 => '',
            2 => 45,
        ],
        312 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 45,
        ],
        313 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 45,
        ],
        314 => [
            0 => 'TextData',
            1 => '
  </table>
  <div id="basket-right">
    <h3>Subtotal ',
            2 => 45,
        ],
        315 => [
            0 => 'VariableStart',
            1 => '',
            2 => 48,
        ],
        316 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 48,
        ],
        317 => [
            0 => 'Dot',
            1 => '.',
            2 => 48,
        ],
        318 => [
            0 => 'Identifier',
            1 => 'total_price',
            2 => 48,
        ],
        319 => [
            0 => 'Pipe',
            1 => '|',
            2 => 48,
        ],
        320 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 48,
        ],
        321 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 48,
        ],
        322 => [
            0 => 'TextData',
            1 => '</h3>
    <input type="image" src="',
            2 => 48,
        ],
        323 => [
            0 => 'VariableStart',
            1 => '',
            2 => 49,
        ],
        324 => [
            0 => 'String',
            1 => '\'update.png\'',
            2 => 49,
        ],
        325 => [
            0 => 'Pipe',
            1 => '|',
            2 => 49,
        ],
        326 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 49,
        ],
        327 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 49,
        ],
        328 => [
            0 => 'TextData',
            1 => '" id="update-cart" name="update" value="Update" />
    <input type="image" src="',
            2 => 49,
        ],
        329 => [
            0 => 'VariableStart',
            1 => '',
            2 => 50,
        ],
        330 => [
            0 => 'String',
            1 => '\'checkout.png\'',
            2 => 50,
        ],
        331 => [
            0 => 'Pipe',
            1 => '|',
            2 => 50,
        ],
        332 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 50,
        ],
        333 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 50,
        ],
        334 => [
            0 => 'TextData',
            1 => '" name="checkout" value="Checkout" />
    ',
            2 => 50,
        ],
        335 => [
            0 => 'BlockStart',
            1 => '',
            2 => 51,
        ],
        336 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 51,
        ],
        337 => [
            0 => 'Identifier',
            1 => 'additional_checkout_buttons',
            2 => 51,
        ],
        338 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 51,
        ],
        339 => [
            0 => 'TextData',
            1 => '
    <div class="additional-checkout-buttons">
      <p>- or -</p>
      ',
            2 => 51,
        ],
        340 => [
            0 => 'VariableStart',
            1 => '',
            2 => 54,
        ],
        341 => [
            0 => 'Identifier',
            1 => 'content_for_additional_checkout_buttons',
            2 => 54,
        ],
        342 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 54,
        ],
        343 => [
            0 => 'TextData',
            1 => '
    </div>
    ',
            2 => 54,
        ],
        344 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        345 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        346 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        347 => [
            0 => 'TextData',
            1 => '
  </div>
</form>',
            2 => 56,
        ],
        348 => [
            0 => 'BlockStart',
            1 => '',
            2 => 58,
        ],
        349 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 58,
        ],
        350 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 58,
        ],
        351 => [
            0 => 'TextData',
            1 => '
',
            2 => 58,
        ],
    ],
    'performance/tests/vogue/collection.liquid' => [
        0 => [
            0 => 'BlockStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 1,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 1,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 1,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 1,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'by',
            2 => 1,
        ],
        6 => [
            0 => 'Number',
            1 => '12',
            2 => 1,
        ],
        7 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 1,
        ],
        8 => [
            0 => 'BlockStart',
            1 => '',
            2 => 1,
        ],
        9 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 1,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 1,
        ],
        11 => [
            0 => 'Dot',
            1 => '.',
            2 => 1,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 1,
        ],
        13 => [
            0 => 'Dot',
            1 => '.',
            2 => 1,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'size',
            2 => 1,
        ],
        15 => [
            0 => 'Comparison',
            1 => '==',
            2 => 1,
        ],
        16 => [
            0 => 'Number',
            1 => '0',
            2 => 1,
        ],
        17 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 1,
        ],
        18 => [
            0 => 'TextData',
            1 => '
  <strong>No products found in this collection.</strong>',
            2 => 1,
        ],
        19 => [
            0 => 'BlockStart',
            1 => '',
            2 => 2,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 2,
        ],
        21 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 2,
        ],
        22 => [
            0 => 'TextData',
            1 => '
  <h1>',
            2 => 2,
        ],
        23 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 3,
        ],
        25 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 3,
        ],
        27 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        28 => [
            0 => 'TextData',
            1 => '</h1>
  ',
            2 => 3,
        ],
        29 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 4,
        ],
        31 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 4,
        ],
        33 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        34 => [
            0 => 'TextData',
            1 => '
  <table id="gallery">
  ',
            2 => 4,
        ],
        35 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        36 => [
            0 => 'Identifier',
            1 => 'tablerow',
            2 => 6,
        ],
        37 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 6,
        ],
        39 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 6,
        ],
        40 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 6,
        ],
        42 => [
            0 => 'Identifier',
            1 => 'cols',
            2 => 6,
        ],
        43 => [
            0 => 'Colon',
            1 => ':',
            2 => 6,
        ],
        44 => [
            0 => 'Number',
            1 => '3',
            2 => 6,
        ],
        45 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        46 => [
            0 => 'TextData',
            1 => '
    <div class="gallery-image">
      <a href="',
            2 => 6,
        ],
        47 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        49 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 8,
        ],
        51 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        52 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 8,
        ],
        53 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 8,
        ],
        55 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        56 => [
            0 => 'TextData',
            1 => '" title="',
            2 => 8,
        ],
        57 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        58 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        59 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 8,
        ],
        61 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        62 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 8,
        ],
        63 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        64 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 8,
        ],
        65 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        66 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        67 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 8,
        ],
        69 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 8,
        ],
        71 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        72 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 8,
        ],
        73 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        74 => [
            0 => 'Number',
            1 => '50',
            2 => 8,
        ],
        75 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 8,
        ],
        77 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        78 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 8,
        ],
        79 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        81 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 8,
        ],
        83 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        84 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 8,
        ],
        85 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        86 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 8,
        ],
        87 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        88 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 8,
        ],
        89 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        90 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 8,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 8,
        ],
        93 => [
            0 => 'Dot',
            1 => '.',
            2 => 8,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 8,
        ],
        95 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        96 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 8,
        ],
        97 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        98 => [
            0 => 'TextData',
            1 => '" /></a>
    </div>
    <div class="gallery-info">
      <a href="',
            2 => 8,
        ],
        99 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        101 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        102 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 11,
        ],
        103 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 11,
        ],
        105 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        106 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 11,
        ],
        107 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        108 => [
            0 => 'TextData',
            1 => '">',
            2 => 11,
        ],
        109 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        111 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        112 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 11,
        ],
        113 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 11,
        ],
        115 => [
            0 => 'Colon',
            1 => ':',
            2 => 11,
        ],
        116 => [
            0 => 'Number',
            1 => '30',
            2 => 11,
        ],
        117 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        118 => [
            0 => 'TextData',
            1 => '</a><br />
      <small>',
            2 => 11,
        ],
        119 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        120 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        121 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        122 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 12,
        ],
        123 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        124 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 12,
        ],
        125 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        126 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 12,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        129 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        130 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 12,
        ],
        131 => [
            0 => 'Comparison',
            1 => '>',
            2 => 12,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        133 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 12,
        ],
        135 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        136 => [
            0 => 'TextData',
            1 => ' <del>',
            2 => 12,
        ],
        137 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        138 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 12,
        ],
        139 => [
            0 => 'Dot',
            1 => '.',
            2 => 12,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 12,
        ],
        141 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        142 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 12,
        ],
        143 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        144 => [
            0 => 'TextData',
            1 => '</del>',
            2 => 12,
        ],
        145 => [
            0 => 'BlockStart',
            1 => '',
            2 => 12,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 12,
        ],
        147 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 12,
        ],
        148 => [
            0 => 'TextData',
            1 => '</small>
    </div>
  ',
            2 => 12,
        ],
        149 => [
            0 => 'BlockStart',
            1 => '',
            2 => 14,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'endtablerow',
            2 => 14,
        ],
        151 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 14,
        ],
        152 => [
            0 => 'TextData',
            1 => '
  </table>',
            2 => 14,
        ],
        153 => [
            0 => 'BlockStart',
            1 => '',
            2 => 15,
        ],
        154 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 15,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 15,
        ],
        156 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        157 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 15,
        ],
        158 => [
            0 => 'Comparison',
            1 => '>',
            2 => 15,
        ],
        159 => [
            0 => 'Number',
            1 => '1',
            2 => 15,
        ],
        160 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 15,
        ],
        161 => [
            0 => 'TextData',
            1 => '
  <div id="paginate">
    ',
            2 => 15,
        ],
        162 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'paginate',
            2 => 17,
        ],
        164 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        165 => [
            0 => 'Identifier',
            1 => 'default_pagination',
            2 => 17,
        ],
        166 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        167 => [
            0 => 'TextData',
            1 => '
  </div>',
            2 => 17,
        ],
        168 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        169 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 18,
        ],
        170 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        171 => [
            0 => 'BlockStart',
            1 => '',
            2 => 18,
        ],
        172 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 18,
        ],
        173 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 18,
        ],
        174 => [
            0 => 'TextData',
            1 => '
',
            2 => 18,
        ],
        175 => [
            0 => 'BlockStart',
            1 => '',
            2 => 19,
        ],
        176 => [
            0 => 'Identifier',
            1 => 'endpaginate',
            2 => 19,
        ],
        177 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 19,
        ],
        178 => [
            0 => 'TextData',
            1 => '
',
            2 => 19,
        ],
    ],
    'performance/tests/vogue/index.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <div id="about-excerpt">
    ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'assign',
            2 => 2,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 2,
        ],
        4 => [
            0 => 'Equals',
            1 => '=',
            2 => 2,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'pages',
            2 => 2,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 2,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 2,
        ],
        9 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 2,
        ],
        10 => [
            0 => 'BlockStart',
            1 => '',
            2 => 3,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 3,
        ],
        12 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 3,
        ],
        13 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 3,
        ],
        15 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 3,
        ],
        16 => [
            0 => 'String',
            1 => '""',
            2 => 3,
        ],
        17 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 3,
        ],
        18 => [
            0 => 'TextData',
            1 => '
      <h2>',
            2 => 3,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 4,
        ],
        20 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 4,
        ],
        21 => [
            0 => 'Dot',
            1 => '.',
            2 => 4,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 4,
        ],
        23 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 4,
        ],
        24 => [
            0 => 'TextData',
            1 => '</h2>
      ',
            2 => 4,
        ],
        25 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        26 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 5,
        ],
        27 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        28 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 5,
        ],
        29 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        30 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 5,
        ],
        31 => [
            0 => 'BlockStart',
            1 => '',
            2 => 6,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 6,
        ],
        33 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 6,
        ],
        34 => [
            0 => 'TextData',
            1 => '
      In <em>Admin &gt; Blogs &amp; Pages</em>, create a page with the handle <strong><code>frontpage</code></strong> and it will show up here.<br />
      ',
            2 => 6,
        ],
        35 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        36 => [
            0 => 'String',
            1 => '"Learn more about handles"',
            2 => 8,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 8,
        ],
        39 => [
            0 => 'Colon',
            1 => ':',
            2 => 8,
        ],
        40 => [
            0 => 'String',
            1 => '"http://wiki.shopify.com/Handle"',
            2 => 8,
        ],
        41 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        42 => [
            0 => 'TextData',
            1 => '
    ',
            2 => 8,
        ],
        43 => [
            0 => 'BlockStart',
            1 => '',
            2 => 9,
        ],
        44 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 9,
        ],
        45 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 9,
        ],
        46 => [
            0 => 'TextData',
            1 => '
  </div>

  <table id="gallery">
  ',
            2 => 9,
        ],
        47 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'tablerow',
            2 => 13,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        50 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 13,
        ],
        51 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 13,
        ],
        52 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 13,
        ],
        54 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 13,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'cols',
            2 => 13,
        ],
        57 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        58 => [
            0 => 'Number',
            1 => '3',
            2 => 13,
        ],
        59 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 13,
        ],
        60 => [
            0 => 'Colon',
            1 => ':',
            2 => 13,
        ],
        61 => [
            0 => 'Number',
            1 => '12',
            2 => 13,
        ],
        62 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        63 => [
            0 => 'TextData',
            1 => '
    <div class="gallery-image">
      <a href="',
            2 => 13,
        ],
        64 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        66 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        67 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 15,
        ],
        68 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 15,
        ],
        70 => [
            0 => 'Colon',
            1 => ':',
            2 => 15,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 15,
        ],
        72 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        73 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 15,
        ],
        74 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        75 => [
            0 => 'TextData',
            1 => '" title="',
            2 => 15,
        ],
        76 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        78 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 15,
        ],
        80 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 15,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        83 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 15,
        ],
        84 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 15,
        ],
        88 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        89 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 15,
        ],
        90 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        91 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 15,
        ],
        92 => [
            0 => 'Colon',
            1 => ':',
            2 => 15,
        ],
        93 => [
            0 => 'Number',
            1 => '50',
            2 => 15,
        ],
        94 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        95 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 15,
        ],
        96 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        97 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 15,
        ],
        98 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        99 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        100 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        101 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 15,
        ],
        102 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        103 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 15,
        ],
        104 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 15,
        ],
        106 => [
            0 => 'Colon',
            1 => ':',
            2 => 15,
        ],
        107 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 15,
        ],
        108 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        109 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 15,
        ],
        110 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 15,
        ],
        112 => [
            0 => 'Dot',
            1 => '.',
            2 => 15,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 15,
        ],
        114 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        115 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 15,
        ],
        116 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        117 => [
            0 => 'TextData',
            1 => '" /></a>
    </div>
    <div class="gallery-info">
      <a href="',
            2 => 15,
        ],
        118 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        119 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        120 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        121 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 18,
        ],
        122 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        123 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 18,
        ],
        124 => [
            0 => 'Colon',
            1 => ':',
            2 => 18,
        ],
        125 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 18,
        ],
        126 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        127 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 18,
        ],
        128 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        129 => [
            0 => 'TextData',
            1 => '">',
            2 => 18,
        ],
        130 => [
            0 => 'VariableStart',
            1 => '',
            2 => 18,
        ],
        131 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 18,
        ],
        132 => [
            0 => 'Dot',
            1 => '.',
            2 => 18,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 18,
        ],
        134 => [
            0 => 'Pipe',
            1 => '|',
            2 => 18,
        ],
        135 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 18,
        ],
        136 => [
            0 => 'Colon',
            1 => ':',
            2 => 18,
        ],
        137 => [
            0 => 'Number',
            1 => '30',
            2 => 18,
        ],
        138 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 18,
        ],
        139 => [
            0 => 'TextData',
            1 => '</a><br />
      <small>',
            2 => 18,
        ],
        140 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        141 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        142 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        143 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 19,
        ],
        144 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 19,
        ],
        146 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        147 => [
            0 => 'BlockStart',
            1 => '',
            2 => 19,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 19,
        ],
        149 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        150 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        151 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 19,
        ],
        152 => [
            0 => 'Comparison',
            1 => '>',
            2 => 19,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        154 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        155 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 19,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 19,
        ],
        157 => [
            0 => 'TextData',
            1 => ' <del>',
            2 => 19,
        ],
        158 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 19,
        ],
        160 => [
            0 => 'Dot',
            1 => '.',
            2 => 19,
        ],
        161 => [
            0 => 'Identifier',
            1 => 'compare_at_price_max',
            2 => 19,
        ],
        162 => [
            0 => 'Pipe',
            1 => '|',
            2 => 19,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 19,
        ],
        164 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        165 => [
            0 => 'TextData',
            1 => '</del>',
            2 => 19,
        ],
        166 => [
            0 => 'BlockStart',
            1 => '',
            2 => 19,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 19,
        ],
        168 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 19,
        ],
        169 => [
            0 => 'TextData',
            1 => '</small>
    </div>
  ',
            2 => 19,
        ],
        170 => [
            0 => 'BlockStart',
            1 => '',
            2 => 21,
        ],
        171 => [
            0 => 'Identifier',
            1 => 'endtablerow',
            2 => 21,
        ],
        172 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 21,
        ],
        173 => [
            0 => 'TextData',
            1 => '
  </table>
',
            2 => 21,
        ],
    ],
    'performance/tests/vogue/page.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '  <h1>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 1,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 1,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 1,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
        6 => [
            0 => 'TextData',
            1 => '</h1>
  ',
            2 => 1,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 2,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page',
            2 => 2,
        ],
        9 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 2,
        ],
        11 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 2,
        ],
        12 => [
            0 => 'TextData',
            1 => '

',
            2 => 2,
        ],
    ],
    'performance/tests/vogue/product.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<div id="product-left">
  ',
            2 => 1,
        ],
        1 => [
            0 => 'BlockStart',
            1 => '',
            2 => 2,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 2,
        ],
        3 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 2,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 2,
        ],
        5 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 2,
        ],
        6 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        7 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 2,
        ],
        8 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 2,
        ],
        9 => [
            0 => 'BlockStart',
            1 => '',
            2 => 2,
        ],
        10 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 2,
        ],
        11 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 2,
        ],
        12 => [
            0 => 'Dot',
            1 => '.',
            2 => 2,
        ],
        13 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 2,
        ],
        14 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 2,
        ],
        15 => [
            0 => 'TextData',
            1 => '<div id="product-image">
    <a href="',
            2 => 2,
        ],
        16 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        17 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 3,
        ],
        18 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        19 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 3,
        ],
        20 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        21 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 3,
        ],
        22 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        23 => [
            0 => 'TextData',
            1 => '" rel="lightbox[images]" title="',
            2 => 3,
        ],
        24 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        25 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 3,
        ],
        26 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        27 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 3,
        ],
        28 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        29 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 3,
        ],
        30 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        31 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 3,
        ],
        32 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        33 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 3,
        ],
        34 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        35 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 3,
        ],
        36 => [
            0 => 'Colon',
            1 => ':',
            2 => 3,
        ],
        37 => [
            0 => 'String',
            1 => '\'medium\'',
            2 => 3,
        ],
        38 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        39 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 3,
        ],
        40 => [
            0 => 'VariableStart',
            1 => '',
            2 => 3,
        ],
        41 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 3,
        ],
        42 => [
            0 => 'Dot',
            1 => '.',
            2 => 3,
        ],
        43 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 3,
        ],
        44 => [
            0 => 'Pipe',
            1 => '|',
            2 => 3,
        ],
        45 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 3,
        ],
        46 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 3,
        ],
        47 => [
            0 => 'TextData',
            1 => '" /></a>
  </div>',
            2 => 3,
        ],
        48 => [
            0 => 'BlockStart',
            1 => '',
            2 => 4,
        ],
        49 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 4,
        ],
        50 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 4,
        ],
        51 => [
            0 => 'TextData',
            1 => '
  <div class="product-images">
    <a href="',
            2 => 4,
        ],
        52 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        53 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 6,
        ],
        54 => [
            0 => 'Pipe',
            1 => '|',
            2 => 6,
        ],
        55 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 6,
        ],
        56 => [
            0 => 'Colon',
            1 => ':',
            2 => 6,
        ],
        57 => [
            0 => 'String',
            1 => '\'large\'',
            2 => 6,
        ],
        58 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        59 => [
            0 => 'TextData',
            1 => '" rel="lightbox[images]" title="',
            2 => 6,
        ],
        60 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        61 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        62 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        63 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 6,
        ],
        64 => [
            0 => 'Pipe',
            1 => '|',
            2 => 6,
        ],
        65 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 6,
        ],
        66 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        67 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 6,
        ],
        68 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        69 => [
            0 => 'Identifier',
            1 => 'image',
            2 => 6,
        ],
        70 => [
            0 => 'Pipe',
            1 => '|',
            2 => 6,
        ],
        71 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 6,
        ],
        72 => [
            0 => 'Colon',
            1 => ':',
            2 => 6,
        ],
        73 => [
            0 => 'String',
            1 => '\'small\'',
            2 => 6,
        ],
        74 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        75 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 6,
        ],
        76 => [
            0 => 'VariableStart',
            1 => '',
            2 => 6,
        ],
        77 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 6,
        ],
        78 => [
            0 => 'Dot',
            1 => '.',
            2 => 6,
        ],
        79 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 6,
        ],
        80 => [
            0 => 'Pipe',
            1 => '|',
            2 => 6,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 6,
        ],
        82 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 6,
        ],
        83 => [
            0 => 'TextData',
            1 => '" /></a>
  </div>',
            2 => 6,
        ],
        84 => [
            0 => 'BlockStart',
            1 => '',
            2 => 7,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 7,
        ],
        86 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 7,
        ],
        87 => [
            0 => 'BlockStart',
            1 => '',
            2 => 7,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 7,
        ],
        89 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 7,
        ],
        90 => [
            0 => 'TextData',
            1 => '
</div>
<div id="product-right">
  <h1>',
            2 => 7,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 10,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 10,
        ],
        93 => [
            0 => 'Dot',
            1 => '.',
            2 => 10,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 10,
        ],
        95 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 10,
        ],
        96 => [
            0 => 'TextData',
            1 => '</h1>
  ',
            2 => 10,
        ],
        97 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 11,
        ],
        99 => [
            0 => 'Dot',
            1 => '.',
            2 => 11,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 11,
        ],
        101 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        102 => [
            0 => 'TextData',
            1 => '

  ',
            2 => 11,
        ],
        103 => [
            0 => 'BlockStart',
            1 => '',
            2 => 13,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 13,
        ],
        105 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 13,
        ],
        106 => [
            0 => 'Dot',
            1 => '.',
            2 => 13,
        ],
        107 => [
            0 => 'Identifier',
            1 => 'available',
            2 => 13,
        ],
        108 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 13,
        ],
        109 => [
            0 => 'TextData',
            1 => '
  <form action="/cart/add" method="post">

    <div id="product-variants">
      <div id="price-field"></div>

      <select id="product-select" name=\'id\'>
        ',
            2 => 13,
        ],
        110 => [
            0 => 'BlockStart',
            1 => '',
            2 => 20,
        ],
        111 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 20,
        ],
        112 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 20,
        ],
        113 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 20,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 20,
        ],
        115 => [
            0 => 'Dot',
            1 => '.',
            2 => 20,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'variants',
            2 => 20,
        ],
        117 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 20,
        ],
        118 => [
            0 => 'TextData',
            1 => '
          <option value="',
            2 => 20,
        ],
        119 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        120 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 21,
        ],
        121 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        122 => [
            0 => 'Identifier',
            1 => 'id',
            2 => 21,
        ],
        123 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        124 => [
            0 => 'TextData',
            1 => '">',
            2 => 21,
        ],
        125 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        126 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 21,
        ],
        127 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 21,
        ],
        129 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        130 => [
            0 => 'TextData',
            1 => ' - ',
            2 => 21,
        ],
        131 => [
            0 => 'VariableStart',
            1 => '',
            2 => 21,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'variant',
            2 => 21,
        ],
        133 => [
            0 => 'Dot',
            1 => '.',
            2 => 21,
        ],
        134 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 21,
        ],
        135 => [
            0 => 'Pipe',
            1 => '|',
            2 => 21,
        ],
        136 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 21,
        ],
        137 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 21,
        ],
        138 => [
            0 => 'TextData',
            1 => '</option>
        ',
            2 => 21,
        ],
        139 => [
            0 => 'BlockStart',
            1 => '',
            2 => 22,
        ],
        140 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 22,
        ],
        141 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 22,
        ],
        142 => [
            0 => 'TextData',
            1 => '
      </select>
    </div>

    <input type="image" src="',
            2 => 22,
        ],
        143 => [
            0 => 'VariableStart',
            1 => '',
            2 => 26,
        ],
        144 => [
            0 => 'String',
            1 => '\'purchase.png\'',
            2 => 26,
        ],
        145 => [
            0 => 'Pipe',
            1 => '|',
            2 => 26,
        ],
        146 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 26,
        ],
        147 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 26,
        ],
        148 => [
            0 => 'TextData',
            1 => '" name="add" value="Purchase" id="purchase" />
  </form>
  ',
            2 => 26,
        ],
        149 => [
            0 => 'BlockStart',
            1 => '',
            2 => 28,
        ],
        150 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 28,
        ],
        151 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 28,
        ],
        152 => [
            0 => 'TextData',
            1 => '
    <p class="bold-red">This product is temporarily unavailable</p>
  ',
            2 => 28,
        ],
        153 => [
            0 => 'BlockStart',
            1 => '',
            2 => 30,
        ],
        154 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 30,
        ],
        155 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 30,
        ],
        156 => [
            0 => 'TextData',
            1 => '

  <div id="product-details">
    <strong>Continue Shopping</strong><br />
    Browse more ',
            2 => 30,
        ],
        157 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        158 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        159 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        160 => [
            0 => 'Identifier',
            1 => 'type',
            2 => 34,
        ],
        161 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        162 => [
            0 => 'Identifier',
            1 => 'link_to_type',
            2 => 34,
        ],
        163 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        164 => [
            0 => 'TextData',
            1 => ' or additional ',
            2 => 34,
        ],
        165 => [
            0 => 'VariableStart',
            1 => '',
            2 => 34,
        ],
        166 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 34,
        ],
        167 => [
            0 => 'Dot',
            1 => '.',
            2 => 34,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'vendor',
            2 => 34,
        ],
        169 => [
            0 => 'Pipe',
            1 => '|',
            2 => 34,
        ],
        170 => [
            0 => 'Identifier',
            1 => 'link_to_vendor',
            2 => 34,
        ],
        171 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 34,
        ],
        172 => [
            0 => 'TextData',
            1 => ' products.
  </div>
</div>


<script type="text/javascript">
<!--
  // mootools callback for multi variants dropdown selector
  var selectCallback = function(variant, selector) {
    if (variant && variant.available == true) {
      // selected a valid variant
      $(\'purchase\').removeClass(\'disabled\'); // remove unavailable class from add-to-cart button
      $(\'purchase\').disabled = false;           // reenable add-to-cart button
      $(\'price-field\').innerHTML = Shopify.formatMoney(variant.price, "',
            2 => 34,
        ],
        173 => [
            0 => 'VariableStart',
            1 => '',
            2 => 47,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 47,
        ],
        175 => [
            0 => 'Dot',
            1 => '.',
            2 => 47,
        ],
        176 => [
            0 => 'Identifier',
            1 => 'money_with_currency_format',
            2 => 47,
        ],
        177 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 47,
        ],
        178 => [
            0 => 'TextData',
            1 => '");  // update price field
    } else {
      // variant doesn\'t exist
      $(\'purchase\').addClass(\'disabled\');      // set add-to-cart button to unavailable class
      $(\'purchase\').disabled = true;              // disable add-to-cart button
      $(\'price-field\').innerHTML = (variant) ? "Sold Out" : "Unavailable"; // update price-field message
    }
  };

  // initialize multi selector for product
  window.addEvent(\'domready\', function() {
    new Shopify.OptionSelectors("product-select", { product: ',
            2 => 47,
        ],
        179 => [
            0 => 'VariableStart',
            1 => '',
            2 => 58,
        ],
        180 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 58,
        ],
        181 => [
            0 => 'Pipe',
            1 => '|',
            2 => 58,
        ],
        182 => [
            0 => 'Identifier',
            1 => 'json',
            2 => 58,
        ],
        183 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 58,
        ],
        184 => [
            0 => 'TextData',
            1 => ', onVariantSelected: selectCallback });
  });
-->
</script>

',
            2 => 58,
        ],
    ],
    'performance/tests/vogue/theme.liquid' => [
        0 => [
            0 => 'TextData',
            1 => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<title>',
            2 => 1,
        ],
        1 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 5,
        ],
        3 => [
            0 => 'Dot',
            1 => '.',
            2 => 5,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 5,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        6 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 5,
        ],
        7 => [
            0 => 'VariableStart',
            1 => '',
            2 => 5,
        ],
        8 => [
            0 => 'Identifier',
            1 => 'page_title',
            2 => 5,
        ],
        9 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 5,
        ],
        10 => [
            0 => 'TextData',
            1 => '</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

',
            2 => 5,
        ],
        11 => [
            0 => 'VariableStart',
            1 => '',
            2 => 8,
        ],
        12 => [
            0 => 'String',
            1 => '\'stylesheet.css\'',
            2 => 8,
        ],
        13 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        14 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 8,
        ],
        15 => [
            0 => 'Pipe',
            1 => '|',
            2 => 8,
        ],
        16 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 8,
        ],
        17 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 8,
        ],
        18 => [
            0 => 'TextData',
            1 => '

<!-- Additional colour schemes for this theme. If you want to use them, just replace the above line with one of these
',
            2 => 8,
        ],
        19 => [
            0 => 'VariableStart',
            1 => '',
            2 => 11,
        ],
        20 => [
            0 => 'String',
            1 => '\'caramel.css\'',
            2 => 11,
        ],
        21 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        22 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 11,
        ],
        23 => [
            0 => 'Pipe',
            1 => '|',
            2 => 11,
        ],
        24 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 11,
        ],
        25 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 11,
        ],
        26 => [
            0 => 'TextData',
            1 => '
',
            2 => 11,
        ],
        27 => [
            0 => 'VariableStart',
            1 => '',
            2 => 12,
        ],
        28 => [
            0 => 'String',
            1 => '\'sea.css\'',
            2 => 12,
        ],
        29 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        30 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 12,
        ],
        31 => [
            0 => 'Pipe',
            1 => '|',
            2 => 12,
        ],
        32 => [
            0 => 'Identifier',
            1 => 'stylesheet_tag',
            2 => 12,
        ],
        33 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 12,
        ],
        34 => [
            0 => 'TextData',
            1 => '
-->

',
            2 => 12,
        ],
        35 => [
            0 => 'VariableStart',
            1 => '',
            2 => 15,
        ],
        36 => [
            0 => 'String',
            1 => '\'mootools.js\'',
            2 => 15,
        ],
        37 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        38 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 15,
        ],
        39 => [
            0 => 'Pipe',
            1 => '|',
            2 => 15,
        ],
        40 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 15,
        ],
        41 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 15,
        ],
        42 => [
            0 => 'TextData',
            1 => '
',
            2 => 15,
        ],
        43 => [
            0 => 'VariableStart',
            1 => '',
            2 => 16,
        ],
        44 => [
            0 => 'String',
            1 => '\'slimbox.js\'',
            2 => 16,
        ],
        45 => [
            0 => 'Pipe',
            1 => '|',
            2 => 16,
        ],
        46 => [
            0 => 'Identifier',
            1 => 'global_asset_url',
            2 => 16,
        ],
        47 => [
            0 => 'Pipe',
            1 => '|',
            2 => 16,
        ],
        48 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 16,
        ],
        49 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 16,
        ],
        50 => [
            0 => 'TextData',
            1 => '
',
            2 => 16,
        ],
        51 => [
            0 => 'VariableStart',
            1 => '',
            2 => 17,
        ],
        52 => [
            0 => 'String',
            1 => '\'option_selection.js\'',
            2 => 17,
        ],
        53 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        54 => [
            0 => 'Identifier',
            1 => 'shopify_asset_url',
            2 => 17,
        ],
        55 => [
            0 => 'Pipe',
            1 => '|',
            2 => 17,
        ],
        56 => [
            0 => 'Identifier',
            1 => 'script_tag',
            2 => 17,
        ],
        57 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 17,
        ],
        58 => [
            0 => 'TextData',
            1 => '

',
            2 => 17,
        ],
        59 => [
            0 => 'VariableStart',
            1 => '',
            2 => 19,
        ],
        60 => [
            0 => 'Identifier',
            1 => 'content_for_header',
            2 => 19,
        ],
        61 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 19,
        ],
        62 => [
            0 => 'TextData',
            1 => '
</head>

<body id="page-',
            2 => 19,
        ],
        63 => [
            0 => 'VariableStart',
            1 => '',
            2 => 22,
        ],
        64 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 22,
        ],
        65 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 22,
        ],
        66 => [
            0 => 'TextData',
            1 => '">

<div id="header">
  <div class="container">
    <div id="logo">
      <h1><a href="/" title="',
            2 => 22,
        ],
        67 => [
            0 => 'VariableStart',
            1 => '',
            2 => 27,
        ],
        68 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 27,
        ],
        69 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        70 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 27,
        ],
        71 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 27,
        ],
        72 => [
            0 => 'TextData',
            1 => '">',
            2 => 27,
        ],
        73 => [
            0 => 'VariableStart',
            1 => '',
            2 => 27,
        ],
        74 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 27,
        ],
        75 => [
            0 => 'Dot',
            1 => '.',
            2 => 27,
        ],
        76 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 27,
        ],
        77 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 27,
        ],
        78 => [
            0 => 'TextData',
            1 => '</a></h1>
    </div>
    <div id="navigation">
      <ul id="navigate">
        <li><a href="/cart">View Cart</a></li>
        ',
            2 => 27,
        ],
        79 => [
            0 => 'BlockStart',
            1 => '',
            2 => 32,
        ],
        80 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 32,
        ],
        81 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 32,
        ],
        82 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 32,
        ],
        83 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 32,
        ],
        84 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        85 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 32,
        ],
        86 => [
            0 => 'Dot',
            1 => '.',
            2 => 32,
        ],
        87 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 32,
        ],
        88 => [
            0 => 'Identifier',
            1 => 'reversed',
            2 => 32,
        ],
        89 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 32,
        ],
        90 => [
            0 => 'TextData',
            1 => '
          <li><a href="',
            2 => 32,
        ],
        91 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        92 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 33,
        ],
        93 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        94 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 33,
        ],
        95 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        96 => [
            0 => 'TextData',
            1 => '">',
            2 => 33,
        ],
        97 => [
            0 => 'VariableStart',
            1 => '',
            2 => 33,
        ],
        98 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 33,
        ],
        99 => [
            0 => 'Dot',
            1 => '.',
            2 => 33,
        ],
        100 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 33,
        ],
        101 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 33,
        ],
        102 => [
            0 => 'TextData',
            1 => '</a></li>
        ',
            2 => 33,
        ],
        103 => [
            0 => 'BlockStart',
            1 => '',
            2 => 34,
        ],
        104 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 34,
        ],
        105 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 34,
        ],
        106 => [
            0 => 'TextData',
            1 => '
      </ul>
    </div>
  </div>
</div>

<div id="mini-header">
  <div class="container">
    <div id="shopping-cart">
      <a href="/cart">Your shopping cart contains ',
            2 => 34,
        ],
        107 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        108 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 43,
        ],
        109 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        110 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 43,
        ],
        111 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        112 => [
            0 => 'TextData',
            1 => ' ',
            2 => 43,
        ],
        113 => [
            0 => 'VariableStart',
            1 => '',
            2 => 43,
        ],
        114 => [
            0 => 'Identifier',
            1 => 'cart',
            2 => 43,
        ],
        115 => [
            0 => 'Dot',
            1 => '.',
            2 => 43,
        ],
        116 => [
            0 => 'Identifier',
            1 => 'item_count',
            2 => 43,
        ],
        117 => [
            0 => 'Pipe',
            1 => '|',
            2 => 43,
        ],
        118 => [
            0 => 'Identifier',
            1 => 'pluralize',
            2 => 43,
        ],
        119 => [
            0 => 'Colon',
            1 => ':',
            2 => 43,
        ],
        120 => [
            0 => 'String',
            1 => '\'item\'',
            2 => 43,
        ],
        121 => [
            0 => 'Comma',
            1 => ',',
            2 => 43,
        ],
        122 => [
            0 => 'String',
            1 => '\'items\'',
            2 => 43,
        ],
        123 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 43,
        ],
        124 => [
            0 => 'TextData',
            1 => '</a>
    </div>
    <div id="search-box">
      <form action="/search" method="get">
        <input type="text" name="q" id="q" />
        <input type="image" src="',
            2 => 43,
        ],
        125 => [
            0 => 'VariableStart',
            1 => '',
            2 => 48,
        ],
        126 => [
            0 => 'String',
            1 => '\'seek.png\'',
            2 => 48,
        ],
        127 => [
            0 => 'Pipe',
            1 => '|',
            2 => 48,
        ],
        128 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 48,
        ],
        129 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 48,
        ],
        130 => [
            0 => 'TextData',
            1 => '" value="Seek" onclick="this.parentNode.submit(); return false;" id="seek" />
      </form>
    </div>
  </div>
</div>

<div id="layout">
  <div class="container">
    <div id="layout-left" ',
            2 => 48,
        ],
        131 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        132 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        133 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 56,
        ],
        134 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 56,
        ],
        135 => [
            0 => 'String',
            1 => '"cart"',
            2 => 56,
        ],
        136 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        137 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        138 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        139 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 56,
        ],
        140 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 56,
        ],
        141 => [
            0 => 'String',
            1 => '"product"',
            2 => 56,
        ],
        142 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        143 => [
            0 => 'TextData',
            1 => 'style="width:619px"',
            2 => 56,
        ],
        144 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        145 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        146 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        147 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        148 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 56,
        ],
        149 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        150 => [
            0 => 'TextData',
            1 => '>',
            2 => 56,
        ],
        151 => [
            0 => 'BlockStart',
            1 => '',
            2 => 56,
        ],
        152 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 56,
        ],
        153 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 56,
        ],
        154 => [
            0 => 'Comparison',
            1 => '==',
            2 => 56,
        ],
        155 => [
            0 => 'String',
            1 => '"search"',
            2 => 56,
        ],
        156 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 56,
        ],
        157 => [
            0 => 'TextData',
            1 => '
      <h1>Search Results</h1>',
            2 => 56,
        ],
        158 => [
            0 => 'BlockStart',
            1 => '',
            2 => 57,
        ],
        159 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 57,
        ],
        160 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 57,
        ],
        161 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 57,
        ],
        162 => [
            0 => 'VariableStart',
            1 => '',
            2 => 58,
        ],
        163 => [
            0 => 'Identifier',
            1 => 'content_for_layout',
            2 => 58,
        ],
        164 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 58,
        ],
        165 => [
            0 => 'TextData',
            1 => '
    </div>',
            2 => 58,
        ],
        166 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        167 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 59,
        ],
        168 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 59,
        ],
        169 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 59,
        ],
        170 => [
            0 => 'String',
            1 => '"cart"',
            2 => 59,
        ],
        171 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        172 => [
            0 => 'BlockStart',
            1 => '',
            2 => 59,
        ],
        173 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 59,
        ],
        174 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 59,
        ],
        175 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 59,
        ],
        176 => [
            0 => 'String',
            1 => '"product"',
            2 => 59,
        ],
        177 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 59,
        ],
        178 => [
            0 => 'TextData',
            1 => '

    <div id="layout-right">
      ',
            2 => 59,
        ],
        179 => [
            0 => 'BlockStart',
            1 => '',
            2 => 62,
        ],
        180 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 62,
        ],
        181 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 62,
        ],
        182 => [
            0 => 'Comparison',
            1 => '==',
            2 => 62,
        ],
        183 => [
            0 => 'String',
            1 => '"index"',
            2 => 62,
        ],
        184 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 62,
        ],
        185 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 62,
        ],
        186 => [
            0 => 'BlockStart',
            1 => '',
            2 => 63,
        ],
        187 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 63,
        ],
        188 => [
            0 => 'Identifier',
            1 => 'blogs',
            2 => 63,
        ],
        189 => [
            0 => 'Dot',
            1 => '.',
            2 => 63,
        ],
        190 => [
            0 => 'Identifier',
            1 => 'news',
            2 => 63,
        ],
        191 => [
            0 => 'Dot',
            1 => '.',
            2 => 63,
        ],
        192 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 63,
        ],
        193 => [
            0 => 'Dot',
            1 => '.',
            2 => 63,
        ],
        194 => [
            0 => 'Identifier',
            1 => 'size',
            2 => 63,
        ],
        195 => [
            0 => 'Comparison',
            1 => '>',
            2 => 63,
        ],
        196 => [
            0 => 'Number',
            1 => '1',
            2 => 63,
        ],
        197 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 63,
        ],
        198 => [
            0 => 'TextData',
            1 => '
      <a href="',
            2 => 63,
        ],
        199 => [
            0 => 'VariableStart',
            1 => '',
            2 => 64,
        ],
        200 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 64,
        ],
        201 => [
            0 => 'Dot',
            1 => '.',
            2 => 64,
        ],
        202 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 64,
        ],
        203 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 64,
        ],
        204 => [
            0 => 'TextData',
            1 => '/blogs/news.xml"><img src="',
            2 => 64,
        ],
        205 => [
            0 => 'VariableStart',
            1 => '',
            2 => 64,
        ],
        206 => [
            0 => 'String',
            1 => '\'feed.png\'',
            2 => 64,
        ],
        207 => [
            0 => 'Pipe',
            1 => '|',
            2 => 64,
        ],
        208 => [
            0 => 'Identifier',
            1 => 'asset_url',
            2 => 64,
        ],
        209 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 64,
        ],
        210 => [
            0 => 'TextData',
            1 => '" alt="Subscribe" class="feed" /></a>
      <h3><a href="/blogs/news">More news</a></h3>
      <ul id="blogs">',
            2 => 64,
        ],
        211 => [
            0 => 'BlockStart',
            1 => '',
            2 => 66,
        ],
        212 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 66,
        ],
        213 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 66,
        ],
        214 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 66,
        ],
        215 => [
            0 => 'Identifier',
            1 => 'blogs',
            2 => 66,
        ],
        216 => [
            0 => 'Dot',
            1 => '.',
            2 => 66,
        ],
        217 => [
            0 => 'Identifier',
            1 => 'news',
            2 => 66,
        ],
        218 => [
            0 => 'Dot',
            1 => '.',
            2 => 66,
        ],
        219 => [
            0 => 'Identifier',
            1 => 'articles',
            2 => 66,
        ],
        220 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 66,
        ],
        221 => [
            0 => 'Colon',
            1 => ':',
            2 => 66,
        ],
        222 => [
            0 => 'Number',
            1 => '6',
            2 => 66,
        ],
        223 => [
            0 => 'Identifier',
            1 => 'offset',
            2 => 66,
        ],
        224 => [
            0 => 'Colon',
            1 => ':',
            2 => 66,
        ],
        225 => [
            0 => 'Number',
            1 => '1',
            2 => 66,
        ],
        226 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 66,
        ],
        227 => [
            0 => 'TextData',
            1 => '
        <li><a href="',
            2 => 66,
        ],
        228 => [
            0 => 'VariableStart',
            1 => '',
            2 => 67,
        ],
        229 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 67,
        ],
        230 => [
            0 => 'Dot',
            1 => '.',
            2 => 67,
        ],
        231 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 67,
        ],
        232 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 67,
        ],
        233 => [
            0 => 'TextData',
            1 => '">',
            2 => 67,
        ],
        234 => [
            0 => 'VariableStart',
            1 => '',
            2 => 67,
        ],
        235 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 67,
        ],
        236 => [
            0 => 'Dot',
            1 => '.',
            2 => 67,
        ],
        237 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 67,
        ],
        238 => [
            0 => 'Pipe',
            1 => '|',
            2 => 67,
        ],
        239 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 67,
        ],
        240 => [
            0 => 'Pipe',
            1 => '|',
            2 => 67,
        ],
        241 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 67,
        ],
        242 => [
            0 => 'Colon',
            1 => ':',
            2 => 67,
        ],
        243 => [
            0 => 'Number',
            1 => '30',
            2 => 67,
        ],
        244 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 67,
        ],
        245 => [
            0 => 'TextData',
            1 => '</a><br />
          <small>',
            2 => 67,
        ],
        246 => [
            0 => 'VariableStart',
            1 => '',
            2 => 68,
        ],
        247 => [
            0 => 'Identifier',
            1 => 'article',
            2 => 68,
        ],
        248 => [
            0 => 'Dot',
            1 => '.',
            2 => 68,
        ],
        249 => [
            0 => 'Identifier',
            1 => 'content',
            2 => 68,
        ],
        250 => [
            0 => 'Pipe',
            1 => '|',
            2 => 68,
        ],
        251 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 68,
        ],
        252 => [
            0 => 'Pipe',
            1 => '|',
            2 => 68,
        ],
        253 => [
            0 => 'Identifier',
            1 => 'truncatewords',
            2 => 68,
        ],
        254 => [
            0 => 'Colon',
            1 => ':',
            2 => 68,
        ],
        255 => [
            0 => 'Number',
            1 => '12',
            2 => 68,
        ],
        256 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 68,
        ],
        257 => [
            0 => 'TextData',
            1 => '</small>
        </li>',
            2 => 68,
        ],
        258 => [
            0 => 'BlockStart',
            1 => '',
            2 => 69,
        ],
        259 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 69,
        ],
        260 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 69,
        ],
        261 => [
            0 => 'TextData',
            1 => '
      </ul>
      ',
            2 => 69,
        ],
        262 => [
            0 => 'BlockStart',
            1 => '',
            2 => 71,
        ],
        263 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 71,
        ],
        264 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 71,
        ],
        265 => [
            0 => 'TextData',
            1 => '
      ',
            2 => 71,
        ],
        266 => [
            0 => 'BlockStart',
            1 => '',
            2 => 72,
        ],
        267 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 72,
        ],
        268 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 72,
        ],
        269 => [
            0 => 'TextData',
            1 => '

      ',
            2 => 72,
        ],
        270 => [
            0 => 'BlockStart',
            1 => '',
            2 => 74,
        ],
        271 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 74,
        ],
        272 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 74,
        ],
        273 => [
            0 => 'Comparison',
            1 => '==',
            2 => 74,
        ],
        274 => [
            0 => 'String',
            1 => '"collection"',
            2 => 74,
        ],
        275 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 74,
        ],
        276 => [
            0 => 'TextData',
            1 => '
      <h3>Collection Tags</h3>
      <div id="tags">',
            2 => 74,
        ],
        277 => [
            0 => 'BlockStart',
            1 => '',
            2 => 76,
        ],
        278 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 76,
        ],
        279 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 76,
        ],
        280 => [
            0 => 'Dot',
            1 => '.',
            2 => 76,
        ],
        281 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 76,
        ],
        282 => [
            0 => 'Dot',
            1 => '.',
            2 => 76,
        ],
        283 => [
            0 => 'Identifier',
            1 => 'size',
            2 => 76,
        ],
        284 => [
            0 => 'Comparison',
            1 => '==',
            2 => 76,
        ],
        285 => [
            0 => 'Number',
            1 => '0',
            2 => 76,
        ],
        286 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 76,
        ],
        287 => [
            0 => 'TextData',
            1 => '
        No tags found.',
            2 => 76,
        ],
        288 => [
            0 => 'BlockStart',
            1 => '',
            2 => 77,
        ],
        289 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 77,
        ],
        290 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 77,
        ],
        291 => [
            0 => 'TextData',
            1 => '
        <span class="tags">',
            2 => 77,
        ],
        292 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        293 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 78,
        ],
        294 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        295 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 78,
        ],
        296 => [
            0 => 'Identifier',
            1 => 'collection',
            2 => 78,
        ],
        297 => [
            0 => 'Dot',
            1 => '.',
            2 => 78,
        ],
        298 => [
            0 => 'Identifier',
            1 => 'tags',
            2 => 78,
        ],
        299 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        300 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        301 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 78,
        ],
        302 => [
            0 => 'Identifier',
            1 => 'current_tags',
            2 => 78,
        ],
        303 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 78,
        ],
        304 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        305 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        306 => [
            0 => 'TextData',
            1 => ' ',
            2 => 78,
        ],
        307 => [
            0 => 'VariableStart',
            1 => '',
            2 => 78,
        ],
        308 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        309 => [
            0 => 'Pipe',
            1 => '|',
            2 => 78,
        ],
        310 => [
            0 => 'Identifier',
            1 => 'highlight_active_tag',
            2 => 78,
        ],
        311 => [
            0 => 'Pipe',
            1 => '|',
            2 => 78,
        ],
        312 => [
            0 => 'Identifier',
            1 => 'link_to_remove_tag',
            2 => 78,
        ],
        313 => [
            0 => 'Colon',
            1 => ':',
            2 => 78,
        ],
        314 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        315 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 78,
        ],
        316 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        317 => [
            0 => 'Identifier',
            1 => 'else',
            2 => 78,
        ],
        318 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        319 => [
            0 => 'TextData',
            1 => ' ',
            2 => 78,
        ],
        320 => [
            0 => 'VariableStart',
            1 => '',
            2 => 78,
        ],
        321 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        322 => [
            0 => 'Pipe',
            1 => '|',
            2 => 78,
        ],
        323 => [
            0 => 'Identifier',
            1 => 'highlight_active_tag',
            2 => 78,
        ],
        324 => [
            0 => 'Pipe',
            1 => '|',
            2 => 78,
        ],
        325 => [
            0 => 'Identifier',
            1 => 'link_to_add_tag',
            2 => 78,
        ],
        326 => [
            0 => 'Colon',
            1 => ':',
            2 => 78,
        ],
        327 => [
            0 => 'Identifier',
            1 => 'tag',
            2 => 78,
        ],
        328 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 78,
        ],
        329 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        330 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 78,
        ],
        331 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        332 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        333 => [
            0 => 'Identifier',
            1 => 'unless',
            2 => 78,
        ],
        334 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 78,
        ],
        335 => [
            0 => 'Dot',
            1 => '.',
            2 => 78,
        ],
        336 => [
            0 => 'Identifier',
            1 => 'last',
            2 => 78,
        ],
        337 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        338 => [
            0 => 'TextData',
            1 => ', ',
            2 => 78,
        ],
        339 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        340 => [
            0 => 'Identifier',
            1 => 'endunless',
            2 => 78,
        ],
        341 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        342 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        343 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 78,
        ],
        344 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        345 => [
            0 => 'TextData',
            1 => '</span>',
            2 => 78,
        ],
        346 => [
            0 => 'BlockStart',
            1 => '',
            2 => 78,
        ],
        347 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 78,
        ],
        348 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 78,
        ],
        349 => [
            0 => 'TextData',
            1 => '
      </div>
      ',
            2 => 78,
        ],
        350 => [
            0 => 'BlockStart',
            1 => '',
            2 => 80,
        ],
        351 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 80,
        ],
        352 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 80,
        ],
        353 => [
            0 => 'TextData',
            1 => '

      <h3>Navigation</h3>
      <ul id="links">
      ',
            2 => 80,
        ],
        354 => [
            0 => 'BlockStart',
            1 => '',
            2 => 84,
        ],
        355 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 84,
        ],
        356 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 84,
        ],
        357 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 84,
        ],
        358 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 84,
        ],
        359 => [
            0 => 'Dot',
            1 => '.',
            2 => 84,
        ],
        360 => [
            0 => 'Identifier',
            1 => 'main-menu',
            2 => 84,
        ],
        361 => [
            0 => 'Dot',
            1 => '.',
            2 => 84,
        ],
        362 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 84,
        ],
        363 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 84,
        ],
        364 => [
            0 => 'TextData',
            1 => '
        <li><a href="',
            2 => 84,
        ],
        365 => [
            0 => 'VariableStart',
            1 => '',
            2 => 85,
        ],
        366 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 85,
        ],
        367 => [
            0 => 'Dot',
            1 => '.',
            2 => 85,
        ],
        368 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 85,
        ],
        369 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 85,
        ],
        370 => [
            0 => 'TextData',
            1 => '">',
            2 => 85,
        ],
        371 => [
            0 => 'VariableStart',
            1 => '',
            2 => 85,
        ],
        372 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 85,
        ],
        373 => [
            0 => 'Dot',
            1 => '.',
            2 => 85,
        ],
        374 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 85,
        ],
        375 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 85,
        ],
        376 => [
            0 => 'TextData',
            1 => '</a></li>
      ',
            2 => 85,
        ],
        377 => [
            0 => 'BlockStart',
            1 => '',
            2 => 86,
        ],
        378 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 86,
        ],
        379 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 86,
        ],
        380 => [
            0 => 'TextData',
            1 => '
      </ul>

      ',
            2 => 86,
        ],
        381 => [
            0 => 'BlockStart',
            1 => '',
            2 => 89,
        ],
        382 => [
            0 => 'Identifier',
            1 => 'if',
            2 => 89,
        ],
        383 => [
            0 => 'Identifier',
            1 => 'template',
            2 => 89,
        ],
        384 => [
            0 => 'Comparison',
            1 => '!=',
            2 => 89,
        ],
        385 => [
            0 => 'String',
            1 => '"page"',
            2 => 89,
        ],
        386 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 89,
        ],
        387 => [
            0 => 'TextData',
            1 => '
      <h3>Featured Products</h3>
      <ul id="featuring">',
            2 => 89,
        ],
        388 => [
            0 => 'BlockStart',
            1 => '',
            2 => 91,
        ],
        389 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 91,
        ],
        390 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 91,
        ],
        391 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 91,
        ],
        392 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 91,
        ],
        393 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        394 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 91,
        ],
        395 => [
            0 => 'Dot',
            1 => '.',
            2 => 91,
        ],
        396 => [
            0 => 'Identifier',
            1 => 'products',
            2 => 91,
        ],
        397 => [
            0 => 'Identifier',
            1 => 'limit',
            2 => 91,
        ],
        398 => [
            0 => 'Colon',
            1 => ':',
            2 => 91,
        ],
        399 => [
            0 => 'Number',
            1 => '6',
            2 => 91,
        ],
        400 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 91,
        ],
        401 => [
            0 => 'TextData',
            1 => '
        <li class="featuring-list">
          <div class="featuring-image">
            <a href="',
            2 => 91,
        ],
        402 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        403 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        404 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        405 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 94,
        ],
        406 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        407 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 94,
        ],
        408 => [
            0 => 'Colon',
            1 => ':',
            2 => 94,
        ],
        409 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 94,
        ],
        410 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        411 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 94,
        ],
        412 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        413 => [
            0 => 'TextData',
            1 => '" title="',
            2 => 94,
        ],
        414 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        415 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        416 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        417 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 94,
        ],
        418 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        419 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 94,
        ],
        420 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        421 => [
            0 => 'TextData',
            1 => ' &mdash; ',
            2 => 94,
        ],
        422 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        423 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        424 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        425 => [
            0 => 'Identifier',
            1 => 'description',
            2 => 94,
        ],
        426 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        427 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 94,
        ],
        428 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        429 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 94,
        ],
        430 => [
            0 => 'Colon',
            1 => ':',
            2 => 94,
        ],
        431 => [
            0 => 'Number',
            1 => '50',
            2 => 94,
        ],
        432 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        433 => [
            0 => 'TextData',
            1 => '"><img src="',
            2 => 94,
        ],
        434 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        435 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        436 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        437 => [
            0 => 'Identifier',
            1 => 'images',
            2 => 94,
        ],
        438 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        439 => [
            0 => 'Identifier',
            1 => 'first',
            2 => 94,
        ],
        440 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        441 => [
            0 => 'Identifier',
            1 => 'product_img_url',
            2 => 94,
        ],
        442 => [
            0 => 'Colon',
            1 => ':',
            2 => 94,
        ],
        443 => [
            0 => 'String',
            1 => '\'icon\'',
            2 => 94,
        ],
        444 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        445 => [
            0 => 'TextData',
            1 => '" alt="',
            2 => 94,
        ],
        446 => [
            0 => 'VariableStart',
            1 => '',
            2 => 94,
        ],
        447 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 94,
        ],
        448 => [
            0 => 'Dot',
            1 => '.',
            2 => 94,
        ],
        449 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 94,
        ],
        450 => [
            0 => 'Pipe',
            1 => '|',
            2 => 94,
        ],
        451 => [
            0 => 'Identifier',
            1 => 'escape',
            2 => 94,
        ],
        452 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 94,
        ],
        453 => [
            0 => 'TextData',
            1 => '" /></a>
          </div>
          <div class="featuring-info">
            <a href="',
            2 => 94,
        ],
        454 => [
            0 => 'VariableStart',
            1 => '',
            2 => 97,
        ],
        455 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 97,
        ],
        456 => [
            0 => 'Dot',
            1 => '.',
            2 => 97,
        ],
        457 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 97,
        ],
        458 => [
            0 => 'Pipe',
            1 => '|',
            2 => 97,
        ],
        459 => [
            0 => 'Identifier',
            1 => 'within',
            2 => 97,
        ],
        460 => [
            0 => 'Colon',
            1 => ':',
            2 => 97,
        ],
        461 => [
            0 => 'Identifier',
            1 => 'collections',
            2 => 97,
        ],
        462 => [
            0 => 'Dot',
            1 => '.',
            2 => 97,
        ],
        463 => [
            0 => 'Identifier',
            1 => 'frontpage',
            2 => 97,
        ],
        464 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 97,
        ],
        465 => [
            0 => 'TextData',
            1 => '">',
            2 => 97,
        ],
        466 => [
            0 => 'VariableStart',
            1 => '',
            2 => 97,
        ],
        467 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 97,
        ],
        468 => [
            0 => 'Dot',
            1 => '.',
            2 => 97,
        ],
        469 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 97,
        ],
        470 => [
            0 => 'Pipe',
            1 => '|',
            2 => 97,
        ],
        471 => [
            0 => 'Identifier',
            1 => 'strip_html',
            2 => 97,
        ],
        472 => [
            0 => 'Pipe',
            1 => '|',
            2 => 97,
        ],
        473 => [
            0 => 'Identifier',
            1 => 'truncate',
            2 => 97,
        ],
        474 => [
            0 => 'Colon',
            1 => ':',
            2 => 97,
        ],
        475 => [
            0 => 'Number',
            1 => '28',
            2 => 97,
        ],
        476 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 97,
        ],
        477 => [
            0 => 'TextData',
            1 => '</a><br />
            <small><span class="light">from</span> ',
            2 => 97,
        ],
        478 => [
            0 => 'VariableStart',
            1 => '',
            2 => 98,
        ],
        479 => [
            0 => 'Identifier',
            1 => 'product',
            2 => 98,
        ],
        480 => [
            0 => 'Dot',
            1 => '.',
            2 => 98,
        ],
        481 => [
            0 => 'Identifier',
            1 => 'price',
            2 => 98,
        ],
        482 => [
            0 => 'Pipe',
            1 => '|',
            2 => 98,
        ],
        483 => [
            0 => 'Identifier',
            1 => 'money',
            2 => 98,
        ],
        484 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 98,
        ],
        485 => [
            0 => 'TextData',
            1 => '</small>
          </div>
        </li>',
            2 => 98,
        ],
        486 => [
            0 => 'BlockStart',
            1 => '',
            2 => 100,
        ],
        487 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 100,
        ],
        488 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 100,
        ],
        489 => [
            0 => 'TextData',
            1 => '
      </ul>
      ',
            2 => 100,
        ],
        490 => [
            0 => 'BlockStart',
            1 => '',
            2 => 102,
        ],
        491 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 102,
        ],
        492 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 102,
        ],
        493 => [
            0 => 'TextData',
            1 => '
    </div>',
            2 => 102,
        ],
        494 => [
            0 => 'BlockStart',
            1 => '',
            2 => 103,
        ],
        495 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 103,
        ],
        496 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 103,
        ],
        497 => [
            0 => 'BlockStart',
            1 => '',
            2 => 103,
        ],
        498 => [
            0 => 'Identifier',
            1 => 'endif',
            2 => 103,
        ],
        499 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 103,
        ],
        500 => [
            0 => 'TextData',
            1 => '
  </div>
</div>

<div id="footer">
  <div id="footer-fader">
    <div class="container">
      <div id="footer-right">',
            2 => 103,
        ],
        501 => [
            0 => 'BlockStart',
            1 => '',
            2 => 110,
        ],
        502 => [
            0 => 'Identifier',
            1 => 'for',
            2 => 110,
        ],
        503 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 110,
        ],
        504 => [
            0 => 'Identifier',
            1 => 'in',
            2 => 110,
        ],
        505 => [
            0 => 'Identifier',
            1 => 'linklists',
            2 => 110,
        ],
        506 => [
            0 => 'Dot',
            1 => '.',
            2 => 110,
        ],
        507 => [
            0 => 'Identifier',
            1 => 'footer',
            2 => 110,
        ],
        508 => [
            0 => 'Dot',
            1 => '.',
            2 => 110,
        ],
        509 => [
            0 => 'Identifier',
            1 => 'links',
            2 => 110,
        ],
        510 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 110,
        ],
        511 => [
            0 => 'TextData',
            1 => '
        ',
            2 => 110,
        ],
        512 => [
            0 => 'VariableStart',
            1 => '',
            2 => 111,
        ],
        513 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 111,
        ],
        514 => [
            0 => 'Dot',
            1 => '.',
            2 => 111,
        ],
        515 => [
            0 => 'Identifier',
            1 => 'title',
            2 => 111,
        ],
        516 => [
            0 => 'Pipe',
            1 => '|',
            2 => 111,
        ],
        517 => [
            0 => 'Identifier',
            1 => 'link_to',
            2 => 111,
        ],
        518 => [
            0 => 'Colon',
            1 => ':',
            2 => 111,
        ],
        519 => [
            0 => 'Identifier',
            1 => 'link',
            2 => 111,
        ],
        520 => [
            0 => 'Dot',
            1 => '.',
            2 => 111,
        ],
        521 => [
            0 => 'Identifier',
            1 => 'url',
            2 => 111,
        ],
        522 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 111,
        ],
        523 => [
            0 => 'TextData',
            1 => ' ',
            2 => 111,
        ],
        524 => [
            0 => 'BlockStart',
            1 => '',
            2 => 111,
        ],
        525 => [
            0 => 'Identifier',
            1 => 'unless',
            2 => 111,
        ],
        526 => [
            0 => 'Identifier',
            1 => 'forloop',
            2 => 111,
        ],
        527 => [
            0 => 'Dot',
            1 => '.',
            2 => 111,
        ],
        528 => [
            0 => 'Identifier',
            1 => 'last',
            2 => 111,
        ],
        529 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 111,
        ],
        530 => [
            0 => 'TextData',
            1 => '&#124;',
            2 => 111,
        ],
        531 => [
            0 => 'BlockStart',
            1 => '',
            2 => 111,
        ],
        532 => [
            0 => 'Identifier',
            1 => 'endunless',
            2 => 111,
        ],
        533 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 111,
        ],
        534 => [
            0 => 'BlockStart',
            1 => '',
            2 => 111,
        ],
        535 => [
            0 => 'Identifier',
            1 => 'endfor',
            2 => 111,
        ],
        536 => [
            0 => 'BlockEnd',
            1 => '',
            2 => 111,
        ],
        537 => [
            0 => 'TextData',
            1 => '
      </div>
      <span id="footer-left">
        Copyright &copy; ',
            2 => 111,
        ],
        538 => [
            0 => 'VariableStart',
            1 => '',
            2 => 114,
        ],
        539 => [
            0 => 'String',
            1 => '"now"',
            2 => 114,
        ],
        540 => [
            0 => 'Pipe',
            1 => '|',
            2 => 114,
        ],
        541 => [
            0 => 'Identifier',
            1 => 'date',
            2 => 114,
        ],
        542 => [
            0 => 'Colon',
            1 => ':',
            2 => 114,
        ],
        543 => [
            0 => 'String',
            1 => '"%Y"',
            2 => 114,
        ],
        544 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 114,
        ],
        545 => [
            0 => 'TextData',
            1 => ' <a href="/">',
            2 => 114,
        ],
        546 => [
            0 => 'VariableStart',
            1 => '',
            2 => 114,
        ],
        547 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 114,
        ],
        548 => [
            0 => 'Dot',
            1 => '.',
            2 => 114,
        ],
        549 => [
            0 => 'Identifier',
            1 => 'name',
            2 => 114,
        ],
        550 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 114,
        ],
        551 => [
            0 => 'TextData',
            1 => '</a>. All Rights Reserved. All prices ',
            2 => 114,
        ],
        552 => [
            0 => 'VariableStart',
            1 => '',
            2 => 114,
        ],
        553 => [
            0 => 'Identifier',
            1 => 'shop',
            2 => 114,
        ],
        554 => [
            0 => 'Dot',
            1 => '.',
            2 => 114,
        ],
        555 => [
            0 => 'Identifier',
            1 => 'currency',
            2 => 114,
        ],
        556 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 114,
        ],
        557 => [
            0 => 'TextData',
            1 => '.<br />
        This website is powered by <a href="http://www.shopify.com">Shopify</a>.
      </span>
    </div>
  </div>
</div>

</body>
</html>
',
            2 => 114,
        ],
    ],
    'inline/hyphenated-identifier-with-number.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'a-5',
            2 => 1,
        ],
        2 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/double-dash-boundary.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'a',
            2 => 1,
        ],
        2 => [
            0 => 'Dash',
            1 => '-',
            2 => 1,
        ],
        3 => [
            0 => 'Dash',
            1 => '-',
            2 => 1,
        ],
        4 => [
            0 => 'Identifier',
            1 => 'b',
            2 => 1,
        ],
        5 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/trailing-dash-boundary.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'a',
            2 => 1,
        ],
        2 => [
            0 => 'Dash',
            1 => '-',
            2 => 1,
        ],
        3 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/question-mark-identifier.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'a-b?',
            2 => 1,
        ],
        2 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/negative-number.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Number',
            1 => '-5',
            2 => 1,
        ],
        2 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/range-literal.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Number',
            1 => '1',
            2 => 1,
        ],
        2 => [
            0 => 'DotDot',
            1 => '..',
            2 => 1,
        ],
        3 => [
            0 => 'Number',
            1 => '5',
            2 => 1,
        ],
        4 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/contains-operator.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Comparison',
            1 => 'contains',
            2 => 1,
        ],
        2 => [
            0 => 'Identifier',
            1 => 'foo',
            2 => 1,
        ],
        3 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
    'inline/vertical-tab-whitespace.liquid' => [
        0 => [
            0 => 'VariableStart',
            1 => '',
            2 => 1,
        ],
        1 => [
            0 => 'Identifier',
            1 => 'foo',
            2 => 1,
        ],
        2 => [
            0 => 'VariableEnd',
            1 => '',
            2 => 1,
        ],
    ],
];
