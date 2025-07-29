
<body>

  <h1>🚀 Pi-Admin 后台管理系统</h1>

  <p>Pi-Admin 是基于 <a href="https://www.workerman.net/webman" target="_blank">Webman</a> 构建的现代化后台管理系统，开箱即用，快速集成，助力高效开发。</p>

<h2>📦 安装步骤</h2>
  <ol>
    <li><strong>安装 Webman 项目</strong>：
      <pre><code>composer create-project workerman/webman:~2.0</code></pre>
    </li>
    <li><strong>安装 Pi-Admin 插件</strong>：
      <pre><code>composer require suqflash/piadmin</code></pre>
    </li>
    <li><strong>执行安装命令</strong>：
      <pre><code>php webman app-plugin:install piadmin</code></pre>
    </li>
    <li><strong>配置环境与数据库</strong>：
      <ul>
        <li>编辑项目根目录的 <code>.env</code> 文件，填写数据库信息</li>
        <li>手动导入提供的 SQL 文件</li>
      </ul>
    </li>
  </ol>

<h2>🛠️ 运行环境</h2>
  <ul>
    <li>操作系统：推荐 <strong>Linux</strong></li>
    <li>PHP：<strong>8.2+</strong></li>
    <li>数据库：<strong>MySQL 5.7</strong></li>
    <li>缓存服务：<strong>Redis</strong></li>
  </ul>

<h2>📚 文档与支持</h2>
  <p>文档正在整理中，敬请期待。如需提前了解，可直接阅读源码。</p>

</body>
