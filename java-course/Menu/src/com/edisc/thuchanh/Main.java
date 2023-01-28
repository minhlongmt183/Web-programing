package com.edisc.thuchanh;

import java.util.Scanner;

import com.edisc.doituong.NhanVien;

public class Main {
	public static void main(String[] args) {
		// khai báo mảng bình thường
		int[] mang = new int[10];
		
		// mảng object
		NhanVien[] nhanViens = new NhanVien[4];
		
		Scanner scanner = new Scanner(System.in);
		System.out.println("Vui long nhap vao gia tri");
		
		for (int i = 0; i < nhanViens.length; i++) {
			String ten = scanner.nextLine();
			String diaChi = scanner.nextLine();
			int tuoi = scanner.nextInt();
			
			// khoi tao doi tuong
			nhanViens[i] = new NhanVien(ten, diaChi, tuoi);
			
			// xoa bo nho diem
			scanner.nextLine();
		}
		
		// in thong tin nhanh vien
		for (NhanVien nv : nhanViens) {
			System.out.println(nv.getTen() + " " + nv.getDiaChi() + " " + nv.getTuoi());
		}
	}

}
