<?php

test('standard output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{ 'John' }}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            John
        </p>
    </div>
    HTML;

    assertTemplateResult($expected, $source);
});

test('variable output with multiple blank lines', function () {
    $source = <<<'LIQUID'
    <div>
        <p>


            {{- 'John' -}}


        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>John</p>
    </div>
    HTML;

    assertTemplateResult($expected, $source);
});

test('tag output with multiple blank lines', function () {
    $source = <<<'LIQUID'
    <div>
        <p>


            {%- if true -%}
            yes
            {%- endif -%}


        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>yes</p>
    </div>
    HTML;

    assertTemplateResult($expected, $source);
});

test('standard tags', function () {
    $whitespace = '        ';

    $source = <<<'LIQUID'
    <div>
        <p>
            {% if true %}
            yes
            {% endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<HTML
    <div>
        <p>
    $whitespace
            yes
    $whitespace
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {% if false %}
            no
            {% endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<HTML
    <div>
        <p>
    $whitespace
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('no trim output', function () {
    $source = <<<'LIQUID'
    <p>{{- 'John' -}}</p>
    LIQUID;
    $expected = <<<'HTML'
    <p>John</p>
    HTML;

    assertTemplateResult($expected, $source);
});

test('no trim tags', function () {
    $source = <<<'LIQUID'
    <p>{%- if true -%}yes{%- endif -%}</p>
    LIQUID;
    $expected = <<<'HTML'
    <p>yes</p>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <p>{%- if false -%}no{%- endif -%}</p>
    LIQUID;
    $expected = <<<'HTML'
    <p></p>
    HTML;
    assertTemplateResult($expected, $source);
});

test('single line outer tag', function () {
    $source = <<<'LIQUID'
    <p> {%- if true %} yes {% endif -%} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> yes </p>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <p> {%- if false %} no {% endif -%} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p></p>
    HTML;
    assertTemplateResult($expected, $source);
});

test('single line inner tag', function () {
    $source = <<<'LIQUID'
    <p> {% if true -%} yes {%- endif %} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> yes </p>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <p> {% if false -%} no {%- endif %} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p>  </p>
    HTML;
    assertTemplateResult($expected, $source);
});

test('single line post tag', function () {
    $source = <<<'LIQUID'
    <p> {% if true -%} yes {% endif -%} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> yes </p>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <p> {% if false -%} no {% endif -%} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> </p>
    HTML;
    assertTemplateResult($expected, $source);
});

test('single line pre tag', function () {
    $source = <<<'LIQUID'
    <p> {%- if true %} yes {%- endif %} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> yes </p>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <p> {%- if false %} no {%- endif %} </p>
    LIQUID;
    $expected = <<<'HTML'
    <p> </p>
    HTML;
    assertTemplateResult($expected, $source);
});

test('pre trim output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{- 'John' }}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>John
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('pre trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if true %}
            yes
            {%- endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            yes
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if false %}
            no
            {%- endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('post trim output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{ 'John' -}}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            John</p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('post trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {% if true -%}
            yes
            {% endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            yes
            </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {% if false -%}
            no
            {% endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('pre and post trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if true %}
            yes
            {% endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            yes
            </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if false %}
            no
            {% endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p></p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('post and pre trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {% if true -%}
            yes
            {%- endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>
            yes
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $whitespace = '        ';
    $source = <<<'LIQUID'
    <div>
        <p>
            {% if false -%}
            no
            {%- endif %}
        </p>
    </div>
    LIQUID;
    $expected = <<<HTML
    <div>
        <p>
    $whitespace
        </p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('trim output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{- 'John' -}}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>John</p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if true -%}
            yes
            {%- endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>yes</p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if false -%}
            no
            {%- endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p></p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('whitespace trim output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{- 'John' -}},
            {{- '30' -}}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>John,30</p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('whitespace trim tags', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if true -%}
            yes
            {%- endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>yes</p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);

    $source = <<<'LIQUID'
    <div>
        <p>
            {%- if false -%}
            no
            {%- endif -%}
        </p>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p></p>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('complex trim output', function () {
    $source = <<<'LIQUID'
    <div>
        <p>
            {{- 'John' -}}
            {{- '30' -}}
        </p>
        <b>
            {{ 'John' -}}
            {{- '30' }}
        </b>
        <i>
            {{- 'John' }}
            {{ '30' -}}
        </i>
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div>
        <p>John30</p>
        <b>
            John30
        </b>
        <i>John
            30</i>
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('complex trim', function () {
    $source = <<<'LIQUID'
    <div>
        {%- if true -%}
            {%- if true -%}
                <p>
                    {{- 'John' -}}
                </p>
            {%- endif -%}
        {%- endif -%}
    </div>
    LIQUID;
    $expected = <<<'HTML'
    <div><p>John</p></div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('right trim followed by tag', function () {
    $source = <<<'LIQUID'
    {{ "a" -}}{{ "b" }} c
    LIQUID;
    $expected = <<<'HTML'
    ab c
    HTML;
    assertTemplateResult($expected, $source);
});

test('raw output', function () {
    $whitespace = '    ';
    $source = <<<'LIQUID'
    <div>
        {% raw %}
            {%- if true -%}
                <p>
                    {{- 'John' -}}
                </p>
            {%- endif -%}
        {% endraw %}
    </div>
    LIQUID;
    $expected = <<<HTML
    <div>
    $whitespace
            {%- if true -%}
                <p>
                    {{- 'John' -}}
                </p>
            {%- endif -%}
    $whitespace
    </div>
    HTML;
    assertTemplateResult($expected, $source);
});

test('pre trim blank preceding text', function () {
    $source = <<<'LIQUID'

    {%- raw %}{% endraw %}
    LIQUID;
    assertTemplateResult('', $source);

    $source = <<<'LIQUID'

    {%- if true %}{% endif %}
    LIQUID;
    assertTemplateResult('', $source);

    $source = <<<'LIQUID'
    {{ 'B' }}
    {%- if true %}C{% endif %}
    LIQUID;
    assertTemplateResult('BC', $source);
});

test('trim blank', function () {
    assertTemplateResult('foobar', 'foo {{--}} bar');
});
