# 「Laravel电商项目」学习记录

学习过程：

第一遍：2020.3.19 - 2020.5.19

## 框架学习

Laravel框架知识点：
- 授权策略类
- 消息队列（延迟队列）
- 容器注入
- 事件监听
    - 发送邮件 
    - 增减库存
- 延迟任务
- 邮件发送
- 异常类
- 日志记录
- 数据迁移
- 工厂文件使用 - 批量创建测试数据

框架类文件：
- RouteServiceProvider
- AuthServiceProvider

第三方库
- 生成二维码  endroid/qr-code
- 支付 yansongda/pay
    - 支付宝支付
    - 微信支付
- 后台模板 laravel-admin     

授权策略的使用：

> authorize('own', $user_address) 方法会获取第二个参数 $user_address 的类名: App\Models\UserAddress，然后执行我们之前在 AuthServiceProvider 类中定义的自动寻找逻辑，在这里找到的类就是 App\Policies\UserAddressPolicy，之后会实例化这个策略类，再调用名为 own() 方法，如果 own() 方法返回 false 则会抛出一个未授权的异常

前端包：
- FontAwesome: 图标icon
- sweetalert: 弹框二次确认


## 程序功能设计


## N. 阅读资源

1、资源：https://learnku.com/courses/laravel-shop/6.x/module-division/5617
2、开发环境部署：https://learnku.com/docs/laravel-development-environment/5.5
3、项目开源源码：https://github.com/summerblue/laravel-shop/tree/L05_5.8
4、laravel-admin 文档：https://laravel-admin.org/docs/zh/model-form-fields#%E5%AF%8C%E6%96%87%E6%9C%AC%E7%BC%96%E8%BE%91%E6%A1%86
5、开发规范：https://learnku.com/docs/laravel-specification/7.x/code-style/7598
6、如何使用Service模式 ：https://www.kancloud.cn/curder/laravel/408485
7、微信支付商户平台：https://pay.weixin.qq.com/ 
8、支付宝商户平台：https://openhome.alipay.com/platform/appDaily.htm?tab=info
9、支付库：yansongda/pay
10、临时的外网可访问网址（用于获取第三方回调请求数据） http://requestbin.net/
N、其他：
 提问的智慧：  https://learnku.com/docs/guide/smart-questions/2032
