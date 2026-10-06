<?php

describe('rendering with template backends', function () {
    test('standard output', function (bool $compiled) {
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

        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('variable output with multiple blank lines', function (bool $compiled) {
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

        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('tag output with multiple blank lines', function (bool $compiled) {
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

        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('standard tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('no trim output', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p>{{- 'John' -}}</p>
    LIQUID;
        $expected = <<<'HTML'
    <p>John</p>
    HTML;

        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('no trim tags', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p>{%- if true -%}yes{%- endif -%}</p>
    LIQUID;
        $expected = <<<'HTML'
    <p>yes</p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
    <p>{%- if false -%}no{%- endif -%}</p>
    LIQUID;
        $expected = <<<'HTML'
    <p></p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('single line outer tag', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p> {%- if true %} yes {% endif -%} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> yes </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
    <p> {%- if false %} no {% endif -%} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p></p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('single line inner tag', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p> {% if true -%} yes {%- endif %} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> yes </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
    <p> {% if false -%} no {%- endif %} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p>  </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('single line post tag', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p> {% if true -%} yes {% endif -%} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> yes </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
    <p> {% if false -%} no {% endif -%} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('single line pre tag', function (bool $compiled) {
        $source = <<<'LIQUID'
    <p> {%- if true %} yes {%- endif %} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> yes </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
    <p> {%- if false %} no {%- endif %} </p>
    LIQUID;
        $expected = <<<'HTML'
    <p> </p>
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('pre trim output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('pre trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('post trim output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('post trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('pre and post trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('post and pre trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('trim output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('whitespace trim output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('whitespace trim tags', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);

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
        assertTemplateResult($expected, $source, compiled: $compiled);

        $source = <<<'LIQUID'
          {%- comment -%}123{%- endcomment -%}Hello!
    LIQUID;
        assertTemplateResult('Hello!', $source, compiled: $compiled);

        $source = <<<'LIQUID'
    {%- comment -%}123{%- endcomment -%}     Hello!
    LIQUID;
        assertTemplateResult('Hello!', $source, compiled: $compiled);

        $source = <<<'LIQUID'
          {%- comment -%}123{%- endcomment -%}     Hello!
    LIQUID;
        assertTemplateResult('Hello!', $source, compiled: $compiled);

        $source = <<<'LIQUID'
    {%- comment %}Whitespace control!{% endcomment -%}
    Hello!
    LIQUID;
        assertTemplateResult('Hello!', $source, compiled: $compiled);
    });

    test('complex trim output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('complex trim', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('right trim followed by tag', function (bool $compiled) {
        $source = <<<'LIQUID'
    {{ "a" -}}{{ "b" }} c
    LIQUID;
        $expected = <<<'HTML'
    ab c
    HTML;
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('raw output', function (bool $compiled) {
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
        assertTemplateResult($expected, $source, compiled: $compiled);
    });

    test('pre trim blank preceding text', function (bool $compiled) {
        $source = <<<'LIQUID'

    {%- raw %}{% endraw %}
    LIQUID;
        assertTemplateResult('', $source, compiled: $compiled);

        $source = <<<'LIQUID'

    {%- if true %}{% endif %}
    LIQUID;
        assertTemplateResult('', $source, compiled: $compiled);

        $source = <<<'LIQUID'
    {{ 'B' }}
    {%- if true %}C{% endif %}
    LIQUID;
        assertTemplateResult('BC', $source, compiled: $compiled);
    });

    test('trim blank', function (bool $compiled) {
        assertTemplateResult('foobar', 'foo {{--}} bar', compiled: $compiled);
    });
})->with('template backends');
