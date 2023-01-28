package com.edisc.main;

import java.util.Scanner;

import com.edisc.doituong.Book;
import com.edisc.hinhhoc.HinhTron;
import com.edisc.hinhhoc.HinhVuong;

public class Main {

	public static void main(String[] args) {
		// TODO Auto-generated method stub
//		Scann
		System.out.println("Ket thuc chuong trinh");
		
		Scanner scanner = new Scanner(System.in);
//		
//		System.out.println("Vui long lua chon:");
//		System.out.println("1 - Tinh chu vi va dien tich hinh vuong. Vui long nhap canh hinh vuong.");
//		System.out.println("2 - Tinh chu vi va dien tich hinh tron. Vui long nhap canh hinh vuong.");
//		
//		int choice = scanner.nextInt();
//		
//		switch (choice) {
//		case 1:
//			System.out.println("Ban da lua chon 1");
//			float canhHinhVuong = scanner.nextInt();
//			System.out.println("Chu vi hinh vuong la " + HinhVuong.chuVi(canhHinhVuong));
//			System.out.println("Dien tich hinh vuong la " + HinhVuong.dienTich(canhHinhVuong));
//			break;
//		case 2:
//			System.out.println("Ban da lua chon 2");
//			float banKinhHinhTron = scanner.nextFloat();
//			System.out.println("Chu vi hinh tron " + HinhTron.chuVi(banKinhHinhTron));
//			System.out.println("Dien tich hinh tron " + HinhTron.dienTich(banKinhHinhTron));
//			break;
//		default:
//			System.out.println("Ban chua lua chon");
//			
//		}
		// constructor mac dinh
		Book book = new Book();
		book.setTen("A");
		book.setMoTa("mac dinh");
		book.setPrice(3.5);
		
		// constructor 1
		Book book1 = new Book("tenA", "moTa: A");
		System.out.println(book1.getTen());
		

	}

}
