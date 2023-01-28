package com.edisc.domain;

public class Main {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
		
		NhanVien nhanVienA = new NhanVien();
		
		// các thuộc tính kế thừa từ cha - person
		nhanVienA.ten = "Nguyen Van A";
		nhanVienA.diaChi = "Ha Noi";
		nhanVienA.luong = 16.3;
		
		
		// các hàm kế thừa từ cha - person
		System.out.println(nhanVienA.getTen() + " " + nhanVienA.getDiaChi()  + " " + nhanVienA.getLuong());


	}

}
