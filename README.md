# Web-programing  
---
## Project at HCMUT


---
## HTML  
### HTML Introduction
### HTML Editors  
### HTML Basic Examples  
#### HTML Documents  
- Khai báo với `<!DOCTYPE html>`  
- Bắt đầu bằng `<html>` và kết thúc bằng `</html>`  
- Những phần có thể xem được nằm ở giữa `<body>` và `<\body>`  
```html
<!DOCTYPE html>
<html>
    <body>

        <h1>My First Heading</h1>
        <p>My first paragraph.</p>

    </body>
</html>
```  
#### Khai báo <!DOCTYPE>  
- Thể hiện loại tài liệu và giúp browser thể hiện trang web một cách đúng.  
- Chỉ xuất hiện 1 lần, ở đầu trang  
- `<!DOCTYPE>` không phân biệt chữ hoa và chữ thường  
- Khai báo cho HTML5 có dạng  
```html
<!DOCTYPE html>  
```
#### HTML Headings  
- Định nghĩa từ `<h1>` đến `<h6>`  
#### HTML Paragraphs  
```html
<p>This is a paragraph.</p>
<p>This is another paragraph.</p>
```  
#### HTML Links  
```html  
<a href="https://google.com.vn">This is a link</a>
```
#### HTML Images  
```html 
<img src="w3schools.jpg" alt="W3Schools.com" width="104" height="142">
```

---
## HTML Elements  
### HTML Elements  
- HTML Element là tất cả mọi thứ nằm giữa `tag`.  
```html
<tagname>Nội dung ở đây... </tagname>
```  
Ví dụ:  
```html
<h1>My First Heading </h1>
<p> My first paragraph </p>
```
- Một số HTML elements không có nội dung (ví dụ như `<br>`). Những thành phần này được gọi là `empty elements`. Empty elements không có tag đóng.  
### Nested HTML Elements  
- Những phần tử của HTML có thể lồng vào nhau, nghĩa là phần tử này có thể chứa phần tử kia.  
- Tất cả tài liệu HTML đều chứa những phần tử html lồng nhau.  
- Ví dụ dễ thấy về việc chứa các phần tử html lồng nhau. 
```html
<!DOCTYPE html>
<html>
    <body>

        <h1>My First Heading</h1>
        <p>My First paragraph</p>

    </body>
</html>
```  
- Trong đó:  
    - `<html></html>` được xem là phần tử gốc (root) của toàn bộ tài liệu HTML  
    - Bên trong `<html>` là phần tử  `<body>`  
    ```html
        <body>

        <h1>My First Heading</h1>
        <p>My First paragraph</p>

    </body>
    ```  
    - trong phần tử `<body>` có 2 phần tử con: `<h1>` và `<p>`  
    ```html
        <h1>My First Heading</h1>
        <p>My First paragraph</p>
    ```
    - Trong đó, 
        - `<h1>` định nghĩa một tiêu đề
        - `<p>` định nghĩa một đoạn văn bản.  
### Never Skip the End Tag  
### Empty HTML Elements  
### HTML is Not Case Sensitive  
- Không phân biệt chữ hoa thường với các tags của html:  
```html
<P> means the same as <p>
```  
- Tuy nhiên, khuyến khích sử dụng chữ viết thường, và chữ viết thường là yêu cầu bắt buộc với loại dữ liệu `XHTML`  
### HTML Tag Reference  
---  
## HTML Attributes  
- HTML attributes cung cấp thông tin cho HTML elements  
### HTML Attributes  

### Forms
#### Một số phần tử:  
- text: nhập văn bản trên một dòng  
- checkbox  
- radio (buttons)  
- select (options)  
- textarea (options)  
- password  
- button  
- submit  
- reset  
- hidden  
- file  
- image  
#### Phương thức và những tính chất hoạt động  
- Gửi dữ liệu lên server  
    - **GET** thêm vào địa chỉ 
    - **POST** gửi dữ liệu riêng   